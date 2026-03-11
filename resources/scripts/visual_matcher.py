import cv2
import numpy as np
import sys
import json
import argparse
import os

# --- HELPER: Proportional Resize ---
def resize_to_max_dim(image, max_dim=1024):
    h, w = image.shape[:2]
    if max(h, w) <= max_dim:
        return image
    scale = max_dim / float(max(h, w))
    nw, nh = int(w * scale), int(h * scale)
    return cv2.resize(image, (nw, nh), interpolation=cv2.INTER_AREA)

def generate_error(msg):
    print(json.dumps({"error": True, "message": msg}))
    sys.exit(0)

# --- MAIN DISCOVERY ENGINE (Registry Intelligence) ---
def process_batch(target_img_path, target_desc, batch_json_path):
    try:
        # 1. Validation
        if not os.path.exists(target_img_path):
            generate_error(f"Reference image not found.")

        if not os.path.exists(batch_json_path):
            generate_error(f"Registry batch data missing.")

        with open(batch_json_path, 'r') as f:
            database_items = json.load(f)

        if not database_items:
            print(json.dumps({"error": False, "matches": [], "message": "No pending reports to compare."}))
            return

        # 2. Target Pre-processing
        img_target_src = cv2.imread(target_img_path)
        img_target = resize_to_max_dim(img_target_src)
        mask_target = np.ones(img_target.shape[:2], dtype=np.uint8) * 255

        # Color Analysis (Target)
        h_target = cv2.calcHist([img_target], [0, 1, 2], mask_target, [8, 8, 8], [0, 256, 0, 256, 0, 256])
        h_target_norm = cv2.normalize(h_target, h_target)

        # Structural Analysis (Target)
        gray_target = cv2.cvtColor(img_target, cv2.COLOR_BGR2GRAY)
        clahe = cv2.createCLAHE(clipLimit=4.0, tileGridSize=(8,8))
        sift = cv2.SIFT_create(nfeatures=10000)
        kp_target, des_target = sift.detectAndCompute(clahe.apply(gray_target), mask_target)

        flann = cv2.FlannBasedMatcher(dict(algorithm=1, trees=5), dict(checks=100))
        results = []

        # 3. Batch Comparison Loop
        for item in database_items:
            db_img_path = item.get("image_path")
            if not os.path.exists(db_img_path): continue

            img_db_src = cv2.imread(db_img_path)
            if img_db_src is None: continue

            img_db = resize_to_max_dim(img_db_src)
            mask_db = np.ones(img_db.shape[:2], dtype=np.uint8) * 255

            # Compare Color
            h_db = cv2.calcHist([img_db], [0, 1, 2], mask_db, [8, 8, 8], [0, 256, 0, 256, 0, 256])
            color_sim = cv2.compareHist(h_target_norm, cv2.normalize(h_db, h_db), cv2.HISTCMP_CORREL)
            color_score = max(0, color_sim) * 100

            # Compare Structure (SIFT)
            gray_db = cv2.cvtColor(img_db, cv2.COLOR_BGR2GRAY)
            kp_db, des_db = sift.detectAndCompute(clahe.apply(gray_db), mask_db)

            final_score = 0
            human_msg = "The item's physical characteristics do not match the target."

            if des_target is not None and des_db is not None:
                matches = flann.knnMatch(des_target, des_db, k=2)
                good = [m_n[0] for m_n in matches if len(m_n) == 2 and m_n[0].distance < 0.75 * m_n[1].distance]

                if len(good) >= 10:
                    src_pts = np.float32([kp_target[m.queryIdx].pt for m in good]).reshape(-1, 1, 2)
                    dst_pts = np.float32([kp_db[m.trainIdx].pt for m in good]).reshape(-1, 1, 2)
                    M, mask_geo = cv2.findHomography(src_pts, dst_pts, cv2.RANSAC, 10.0)

                    if mask_geo is not None:
                        inliers = int(np.sum(mask_geo))

                        if inliers >= 50:
                            struct_score, human_msg = 96, "Exceptional match. Unique physical details are almost identical."
                        elif inliers >= 35:
                            struct_score, human_msg = 88, "Strong match. Visual patterns show a high level of consistency."
                        elif inliers >= 25:
                            struct_score, human_msg = 78, "Likely match. System detected significant physical similarities."
                        else:
                            struct_score, human_msg = 30, "Insufficient structural similarity for a conclusive match."

                        final_score = (struct_score * 0.9) + (color_score * 0.1)
                    else:
                        final_score = color_score * 0.3
                else:
                    final_score = color_score * 0.2

            final_score = min(98, max(0, final_score))

            # Apply 75% Confidence Filter
            if final_score >= 75:
                results.append({
                    "item_id": item.get("id"),
                    "confidence_score": int(final_score),
                    "breakdown": human_msg
                })

        results.sort(key=lambda x: x["confidence_score"], reverse=True)
        print(json.dumps({"error": False, "matches": results[:5]}))

    except Exception as e:
        generate_error(f"Intelligence Processing Interrupted: {str(e)}")

if __name__ == "__main__":
    parser = argparse.ArgumentParser()
    parser.add_argument("target_img")
    parser.add_argument("target_desc") # Swapped to match Controller order
    parser.add_argument("batch_json") # Swapped to match Controller order
    args = parser.parse_args()

    process_batch(args.target_img, args.target_desc, args.batch_json)
