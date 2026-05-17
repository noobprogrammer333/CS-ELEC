# CS-ELEC

CS-ELEC contains NoteAI, a Streamlit-based intelligent knowledge vault
assistant for capturing lesson notes, extracting linguistic metadata, and
querying saved notes through a local Ollama model.

## Getting started

1. Clone the repository.
2. Create and activate a Python virtual environment.
3. Install the Python dependencies:

   ```bash
   pip install -r requirements.txt
   ```

4. Start Ollama and make sure at least one model is available:

   ```bash
   ollama serve
   ollama pull llama3
   ```

5. Run the Streamlit app:

   ```bash
   streamlit run app.py
   ```

> Voice capture uses `PyAudio`, which may require PortAudio system libraries
> before `pip install -r requirements.txt` succeeds on some machines.

## Project structure

```text
app.py            Streamlit NoteAI application
requirements.txt  Python runtime dependencies
```

The app creates `noteai_vault.db` locally at runtime for notes and chat
history. That generated database file is intentionally ignored by Git.

## Features

- Manual text note ingestion.
- Microphone-based speech recognition note capture.
- NLTK tokenization, keyword extraction, lemmatization, and VADER sentiment
  analysis.
- SQLite persistence for notes and chat history.
- Local retrieval-augmented question answering through Ollama.
- Text-to-speech playback for assistant answers.

## Development

- Keep generated files and local environment files out of version control.
- Run `python3 -m py_compile app.py` for a quick syntax check.
- Add tests alongside new implementation work where practical.
