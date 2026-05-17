<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>NLP Intelligent Web Application</title>
    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
      rel="stylesheet"
    >
    <link rel="stylesheet" href="static/css/style.css">
  </head>
  <body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary sticky-top shadow-sm">
      <div class="container">
        <a class="navbar-brand fw-bold" href="#home">NoteAI Lessons</a>
        <button
          class="navbar-toggler"
          type="button"
          data-bs-toggle="collapse"
          data-bs-target="#mainNav"
          aria-controls="mainNav"
          aria-expanded="false"
          aria-label="Toggle navigation"
        >
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="mainNav">
          <ul class="navbar-nav ms-auto">
            <li class="nav-item"><a class="nav-link" href="#notes">Notes</a></li>
            <li class="nav-item"><a class="nav-link" href="#assistant">AI Assistant</a></li>
            <li class="nav-item"><a class="nav-link" href="#history">Q&A History</a></li>
            <li class="nav-item"><a class="nav-link" href="#about">About</a></li>
          </ul>
        </div>
      </div>
    </nav>

    <header id="home" class="hero-section">
      <div class="container py-5">
        <div class="row align-items-center g-4">
          <div class="col-lg-7">
            <p class="text-uppercase text-primary fw-semibold small mb-2">NLP-Based Intelligent Web Application</p>
            <h1 class="display-5 fw-bold">A phone-style notes app with speech-to-text and an AI lesson assistant.</h1>
            <p class="lead text-muted mt-3">
              Create lesson notes by typing or speaking. The system analyzes each note with
              tokenization, lemmatization, sentiment analysis, keyword extraction, and
              classification. Then ask the assistant a question and it scans your notes for
              the lesson you need.
            </p>
            <div class="d-flex flex-wrap gap-2 mt-4">
              <a href="#notes" class="btn btn-primary btn-lg">Create Notes</a>
              <a href="#assistant" class="btn btn-outline-primary btn-lg">Ask Assistant</a>
            </div>
          </div>
          <div class="col-lg-5">
            <div class="feature-card p-4">
              <h2 class="h4 fw-bold mb-3">Selected Project Stack</h2>
              <div class="row g-3">
                <div class="col-6"><span class="badge text-bg-light feature-badge">Frontend: HTML</span></div>
                <div class="col-6"><span class="badge text-bg-light feature-badge">Backend: PHP</span></div>
                <div class="col-6"><span class="badge text-bg-light feature-badge">Database: Supabase</span></div>
                <div class="col-6"><span class="badge text-bg-light feature-badge">NLP: NLTK</span></div>
                <div class="col-6"><span class="badge text-bg-light feature-badge">NLP: spaCy</span></div>
                <div class="col-6"><span class="badge text-bg-light feature-badge">Voice APIs</span></div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </header>

    <main class="container my-5">
      <section id="notes" class="section-anchor mb-5">
        <div class="row g-4">
          <div class="col-lg-7">
            <div class="card shadow-sm h-100">
              <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <div>
                  <h2 class="h4 mb-0">Lesson Notes</h2>
                  <small class="text-muted">Type or speak notes like a cellphone notes app</small>
                </div>
                <button id="clearNotesBtn" class="btn btn-sm btn-outline-danger">Clear Notes</button>
              </div>
              <div class="card-body">
                <form id="noteForm">
                  <label class="form-label fw-semibold" for="noteTitle">Note title</label>
                  <input id="noteTitle" class="form-control mb-3" type="text" placeholder="Example: Software Implementation Lesson">
                  <label class="form-label fw-semibold" for="noteContent">Note content</label>
                  <textarea
                    id="noteContent"
                    class="form-control note-editor"
                    rows="10"
                    placeholder="Type your lesson note here, or press Dictate Note and speak..."
                  ></textarea>
                  <div class="d-flex flex-wrap gap-2 mt-3">
                    <button id="noteVoiceBtn" class="btn btn-outline-secondary" type="button">Dictate Note</button>
                    <button class="btn btn-primary" type="submit">Save Note</button>
                    <span id="noteVoiceStatus" class="small text-muted align-self-center">Speech becomes text inside the note.</span>
                  </div>
                </form>
                <div id="noteStorageNotice" class="alert alert-warning d-none mt-3"></div>
              </div>
            </div>
          </div>

          <div class="col-lg-5">
            <div class="card shadow-sm h-100">
              <div class="card-header bg-white">
                <h2 class="h4 mb-0">Saved Note NLP Output</h2>
                <small class="text-muted">Visible proof of NLP concepts for every note</small>
              </div>
              <div class="card-body">
                <div class="row g-3 mb-3">
                  <div class="col-sm-6">
                    <div class="metric-card">
                      <span class="metric-label">Sentiment</span>
                      <strong id="sentimentResult">Waiting</strong>
                    </div>
                  </div>
                  <div class="col-sm-6">
                    <div class="metric-card">
                      <span class="metric-label">Classification</span>
                      <strong id="classificationResult">Waiting</strong>
                    </div>
                  </div>
                </div>
                <h3 class="h6">Tokenized Note</h3>
                <div id="tokenList" class="token-area mb-3">Save a note to see tokens.</div>
                <h3 class="h6">Lemmatized Terms</h3>
                <div id="lemmaList" class="token-area mb-3">Save a note to see lemmas.</div>
                <h3 class="h6">Extracted Keywords</h3>
                <div id="keywordList" class="token-area mb-3">Save a note to see keywords.</div>
              </div>
            </div>
          </div>
        </div>
      </section>

      <section id="saved-notes" class="section-anchor mb-5">
        <div class="card shadow-sm">
          <div class="card-body p-4">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
              <div>
                <h2 class="h4 mb-0">Saved Notes Vault</h2>
                <small class="text-muted">The assistant scans these notes when answering questions</small>
              </div>
              <button id="refreshNotesBtn" class="btn btn-outline-primary btn-sm">Refresh Notes</button>
            </div>
            <div id="notesNotice" class="alert alert-warning d-none"></div>
            <div id="notesList" class="row g-3"></div>
          </div>
        </div>
      </section>

      <section id="assistant" class="section-anchor mb-5">
        <div class="row g-4">
          <div class="col-lg-7">
            <div class="card shadow-sm h-100">
              <div class="card-header bg-white">
                <h2 class="h4 mb-0">AI Lesson Assistant</h2>
                <small class="text-muted">Ask directly and the assistant scans your notes</small>
              </div>
              <div id="chatBox" class="card-body chat-box" aria-live="polite"></div>
              <div class="card-footer bg-white">
                <form id="chatForm" class="chat-form">
                  <div class="input-group">
                    <input
                      id="userInput"
                      class="form-control"
                      type="text"
                      placeholder="Ask about a lesson from your saved notes..."
                      autocomplete="off"
                    >
                    <button id="voiceBtn" class="btn btn-outline-secondary" type="button">Voice Question</button>
                    <button class="btn btn-primary" type="submit">Ask</button>
                  </div>
                  <div class="d-flex flex-wrap gap-2 mt-3">
                    <button id="speakLastBtn" class="btn btn-outline-primary btn-sm" type="button">Speak Answer</button>
                    <span id="voiceStatus" class="small text-muted align-self-center">Ask with text or voice.</span>
                  </div>
                </form>
              </div>
            </div>
          </div>
          <div class="col-lg-5">
            <div class="card shadow-sm h-100">
              <div class="card-header bg-white">
                <h2 class="h4 mb-0">Matched Notes</h2>
                <small class="text-muted">Sources used by the assistant answer</small>
              </div>
              <div id="matchedNotes" class="card-body">
                <p class="text-muted mb-0">Ask a question to see matching notes.</p>
              </div>
            </div>
          </div>
        </div>
      </section>

      <section id="history" class="section-anchor mb-5">
        <div class="card shadow-sm">
          <div class="card-body p-4">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
              <div>
                <h2 class="h4 mb-0">Assistant Q&A History</h2>
                <small class="text-muted">Questions and answers stored after note retrieval</small>
              </div>
              <button id="refreshHistoryBtn" class="btn btn-outline-primary btn-sm">Refresh</button>
            </div>
            <div id="historyNotice" class="alert alert-warning d-none"></div>
            <div class="table-responsive">
              <table class="table table-hover align-middle">
                <thead>
                  <tr>
                    <th>#</th>
                    <th>Question</th>
                    <th>Answer</th>
                    <th>Matched Notes</th>
                    <th>Created</th>
                  </tr>
                </thead>
                <tbody id="historyTable">
                  <tr><td colspan="5" class="text-muted">No history loaded yet.</td></tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </section>

      <section id="about" class="section-anchor mb-5">
        <div class="row g-4">
          <div class="col-lg-6">
            <div class="card shadow-sm h-100">
              <div class="card-body p-4">
                <h2 class="h4">How the System Works</h2>
                <ol class="workflow-list">
                  <li>User types or dictates a lesson into the note editor.</li>
                  <li>PHP sends the note to the NLTK + spaCy processor.</li>
                  <li>The note is saved in Supabase with tokens, lemmas, sentiment, keywords, and classification.</li>
                  <li>User asks the assistant a question.</li>
                  <li>The assistant scans saved notes and returns the best lesson matches.</li>
                  <li>The answer is shown on screen and can be spoken aloud.</li>
                </ol>
              </div>
            </div>
          </div>
          <div class="col-lg-6">
            <div class="card shadow-sm h-100">
              <div class="card-body p-4">
                <h2 class="h4">Presentation Notes</h2>
                <p class="text-muted">
                  During defense, demonstrate creating a note by speech-to-text, saving NLP
                  metadata, asking the assistant about the lesson, showing matched notes, and
                  using text-to-speech for the answer.
                </p>
                <a class="btn btn-outline-primary" href="#notes">Return to Notes Demo</a>
              </div>
            </div>
          </div>
        </div>
      </section>
    </main>

    <footer class="py-4 bg-dark text-white">
      <div class="container d-flex flex-wrap justify-content-between gap-2">
        <span>NLP-Based Intelligent Web Application Development</span>
        <span>HTML + PHP + Supabase + NLTK + spaCy</span>
      </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="static/js/script.js"></script>
  </body>
</html>
