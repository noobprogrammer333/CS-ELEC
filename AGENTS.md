# AGENTS.md

## Cursor Cloud specific instructions

### System dependencies

PHP 8.x with the `curl` extension is required but not bundled in the base image.
Install before any other step:

```
sudo apt-get update && sudo apt-get install -y php php-curl
```

### Quick reference

| Task | Command |
|------|---------|
| Install Python deps | `pip install -r requirements.txt && python3 -m spacy download en_core_web_sm` |
| PHP lint (all files) | `php -l index.php && php -l api/chat.php && php -l api/analyze.php && php -l api/history.php && php -l api/notes.php` |
| Python compile check | `python3 -m py_compile nlp/nlp_processor.py tests/test_nlp_processor.py` |
| Run tests | `python3 -m unittest tests/test_nlp_processor.py -v` |
| Start dev server | `php -S 127.0.0.1:8000` (from repo root) |
| Test NLP API | `curl -s -X POST http://127.0.0.1:8000/api/analyze.php -H "Content-Type: application/json" -d '{"message": "test message"}'` |

### Supabase

The app requires `SUPABASE_URL` and `SUPABASE_KEY` in `.env` for database persistence.
Without them the app still runs — NLP analysis works, but note storage and chat history
return warnings. Copy `.env.example` to `.env` and fill in credentials if available.

### Gotchas

- The `PYTHON_BIN` value in `.env` must point to the Python interpreter that has `nltk`
  and `spacy` installed (defaults to `python3`). If using a venv, update it accordingly.
- NLTK data (`punkt`, `punkt_tab`, `stopwords`, `vader_lexicon`) is auto-downloaded on
  first NLP call; this requires internet access.
- The spaCy `en_core_web_sm` model is optional — the processor falls back to
  `spacy.blank("en")` with a sentencizer, but keyword/lemma quality is reduced.
