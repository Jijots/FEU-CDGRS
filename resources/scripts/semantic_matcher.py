import sys
import os
import json
import warnings
import difflib

# Suppress warnings
warnings.filterwarnings("ignore", category=FutureWarning)

# Environment Overrides
os.environ['HOME'] = r'C:\Users\Joss'
os.environ['USERPROFILE'] = r'C:\Users\Joss'

# Library Paths
lib_path = r"C:\Users\Joss\AppData\Local\Packages\PythonSoftwareFoundation.Python.3.11_qbz5n2kfra8p0\LocalCache\local-packages\Python311\site-packages"
if lib_path not in sys.path:
    sys.path.append(lib_path)

def generate_output(match_id, score, msg):
    """The Anti-Crash Output: Always exits with 0 so Laravel can read the error."""
    sys.stdout.flush()
    print(json.dumps({
        "matched_student_id": match_id,
        "confidence_score": int(score),
        "visual_score": int(score),
        "breakdown": msg
    }))
    sys.exit(0)

# --- SAFE IMPORTS ---
try:
    import PIL.Image
    import pytesseract
    pytesseract.pytesseract.tesseract_cmd = r'C:\Program Files\Tesseract-OCR\tesseract.exe'
except ImportError as e:
    pass

def semantic_similarity(ocr_text, target_string):
    """Checks for a direct substring match first to fix the difflib math flaw, then falls back to fuzzy matching."""
    if not ocr_text or not target_string: return 0.0

    t1 = str(ocr_text).upper().replace(" ", "").replace("\n", "")
    t2 = str(target_string).upper().replace(" ", "")

    # NEW FIX: If the exact 9-digit ID is found anywhere inside the messy OCR text block, give a high score!
    if len(t2) > 4 and t2 in t1:
        return 0.95

    return difflib.SequenceMatcher(None, t1, t2).ratio()

if __name__ == "__main__":
    try:
        # Args mapping
        name_in = sys.argv[1] if len(sys.argv) > 1 else ""
        id_in = sys.argv[2] if len(sys.argv) > 2 else ""
        prog_in = sys.argv[3] if len(sys.argv) > 3 else ""
        json_file_path = sys.argv[4] if len(sys.argv) > 4 else ""
        image_path = sys.argv[5] if len(sys.argv) > 5 else ""

        # Load Database
        students = []
        if json_file_path and os.path.exists(json_file_path):
            try:
                with open(json_file_path, 'r') as f:
                    students = json.load(f)
            except Exception:
                students = []

        # ==========================================
        # TIER 1: DETERMINISTIC MATCH (Hardware / Manual Input)
        # ==========================================
        for s in students:
            if id_in and str(id_in).strip() == str(s['id_number']).strip():
                generate_output(s['id'], 100, f"Tier 1 Hardware Match: ID {id_in} perfectly verified.")

        img = None
        if image_path and os.path.exists(image_path):
            img = PIL.Image.open(image_path)
        else:
            generate_output(None, 0, "No image provided for scan.")

        # ==========================================
        # TIER 2: LOCAL OFFLINE SEMANTIC MATCH (Tesseract First)
        # ==========================================
        if img:
            try:
                raw_ocr_text = pytesseract.image_to_string(img)
                best_score = 0
                best_match_id = None

                for s in students:
                    name_score = semantic_similarity(raw_ocr_text, s['name'])
                    id_score = semantic_similarity(raw_ocr_text, s['id_number'])
                    highest_local = max(name_score, id_score) * 100

                    if highest_local > best_score:
                        best_score = highest_local
                        best_match_id = s['id']

                # If Tesseract confidently finds the student, EXIT IMMEDIATELY and skip Gemini
                if best_score >= 65:
                    generate_output(best_match_id, best_score, f"Tier 2 Offline Match (Local OCR): {int(best_score)}% confidence.")
            except Exception as e:
                pass # If Tesseract crashes, silently fall through to Gemini

        # ==========================================
        # TIER 3: GEMINI MULTI-MODAL MATCH (Visual + Semantic API Fallback)
        # ==========================================
        api_key = os.environ.get("GOOGLE_API_KEY")
        if api_key and img:
            try:
                import google.generativeai as genai
                genai.configure(api_key=api_key)
                model = genai.GenerativeModel('gemini-1.5-flash')

                prompt = """
                Analyze this image.
                1. VISUAL CHECK: Does this physically look like a legitimate university or school student ID card? (Check for layout, photo placeholder, institutional logos, barcodes, etc.)
                2. SEMANTIC CHECK: If it is an ID, extract the exact Student Name, ID Number, and Program/Course Code printed on it. If you can't read one, leave it blank.

                Return ONLY a raw JSON string (no markdown formatting):
                {
                    "is_valid_id_card": true/false,
                    "extracted_id_number": "number here",
                    "extracted_name": "name here"
                }
                """
                response = model.generate_content([prompt, img])
                result_text = response.text.strip()

                if result_text.startswith("```json"): result_text = result_text[7:-3].strip()
                elif result_text.startswith("```"): result_text = result_text[3:-3].strip()

                ai_data = json.loads(result_text)

                if not ai_data.get("is_valid_id_card"):
                    generate_output(None, 15, "Tier 3 AI Rejection: Image does not appear to be a legitimate student ID card.")

                extracted_id = str(ai_data.get("extracted_id_number", "")).strip()

                for s in students:
                    db_id = str(s['id_number']).strip()
                    if extracted_id and extracted_id == db_id:
                        generate_output(s['id'], 95, f"Tier 3 AI Match: API Visuals verified and ID {extracted_id} matched database.")

            except Exception as e:
                generate_output(None, 0, f"Tier 3 API Failure: {str(e)}")

        # If it reaches here, all 3 tiers failed
        generate_output(None, 0, "All verification tiers exhausted. No match found.")

    except Exception as e:
        generate_output(None, 0, f"System Failure: {str(e)}")
