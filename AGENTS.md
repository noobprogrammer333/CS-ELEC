# AGENTS.md

## Cursor Cloud specific instructions

This is a PHP + Python NLP web application. See `README.md` for full Getting Started steps and project structure.

### Services

| Service | Command | Notes |
|---|---|---|
| PHP dev server | `php -S 127.0.0.1:8000` (from repo root) | Serves frontend and API endpoints |
| Python NLP processor | Invoked automatically by PHP via `proc_open()` | No separate process needed |

### Key environment details

- **PHP 8.3** must be installed with the `curl` extension (`apt install php php-cli php-curl`).
- **Python venv** lives at `.venv/`. The `.env` file sets `PYTHON_BIN=/workspace/.venv/bin/python3` so PHP can find the correct interpreter.
- NLTK data (`punkt`, `punkt_tab`, `stopwords`, `vader_lexicon`) is auto-downloaded on first NLP call but is also pre-downloaded during setup.
- The spaCy `en_core_web_sm` model is installed; the code falls back to `spacy.blank("en")` if missing.
- **Supabase** (external DB) is optional. Chat and text analysis work without it; only history save/load shows warnings with placeholder credentials.

### Validation / testing commands

Documented in `README.md` under "Validation":

```bash
php -l index.php api/chat.php api/analyze.php api/history.php
source .venv/bin/activate && python3 -m py_compile nlp/nlp_processor.py tests/test_nlp_processor.py
source .venv/bin/activate && python3 -m unittest tests/test_nlp_processor.py
```

### Gotchas

- `PYTHON_BIN` in `.env` must point to the venv Python (`.venv/bin/python3`), not the system `python3`, otherwise the NLP processor will fail with missing `nltk`/`spacy` imports.
- The analyze API endpoint (`api/analyze.php`) expects a `message` field (not `text`) in the JSON body.
- PHP's built-in server is single-threaded; NLP subprocess calls (~1s each) block other requests during processing.
