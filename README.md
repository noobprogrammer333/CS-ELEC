# CS-ELEC NLP Assistant

NLP Assistant is a Flask-based web application for the project requirement
"NLP-Based Intelligent Web Application Development." It demonstrates natural
language processing through a chatbot, smart text analyzer, voice input,
speech output, and database-backed conversation history.

## Implemented Requirements

- Selected frontend: HTML
- Supporting frontend assets: CSS, Bootstrap, and JavaScript for styling and
  browser interactivity
- Backend: Python Flask
- Database: SQLite runtime database with SQL export in `database/nlp_assistant.sql`
- NLP features:
  - Tokenization
  - Sentiment analysis
  - Voice-to-text
  - Text-to-speech
  - Chatbot system
  - Keyword extraction
  - Text classification
- Functional navigation: Chatbot, Text Analyzer, History, About
- Required documentation: project documentation, flowchart, and ER diagram

## Getting Started

1. Clone the repository.
2. Create and activate a Python virtual environment:

   ```bash
   python3 -m venv .venv
   source .venv/bin/activate
   ```

3. Install dependencies:

   ```bash
   pip install -r requirements.txt
   ```

4. Run the web application:

   ```bash
   python3 app.py
   ```

5. Open the app in a browser:

   ```text
   http://127.0.0.1:5000
   ```

## Project Structure

```text
app.py                         Flask app and API routes
database.py                    SQLite persistence layer
nlp_engine.py                  Tokenization, sentiment, keywords, classification, chatbot logic
requirements.txt               Python dependencies
templates/index.html           Bootstrap web interface
static/css/style.css           Custom responsive styling
static/js/script.js            AJAX, voice input, speech output, UI rendering
database/nlp_assistant.sql     SQL export/schema deliverable
docs/project_documentation.md  Project documentation content
docs/system_flowchart.mmd      Mermaid system flowchart
docs/er_diagram.mmd            Mermaid ER diagram
```

The app creates `nlp_assistant.db` locally at runtime. Generated database files
are ignored by Git.

## Demo Checklist

1. Send a chat message and show the bot response.
2. Display tokenized input, sentiment, keywords, and classification.
3. Use the Voice button to convert speech into text.
4. Use Speak Last Response for text-to-speech output.
5. Open the History section to show database records.
6. Open the Smart Text Analyzer for standalone NLP analysis.

## Validation

```bash
python3 -m py_compile app.py database.py nlp_engine.py
```
