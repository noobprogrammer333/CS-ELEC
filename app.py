from flask import Flask, jsonify, render_template, request

from database import clear_chat_history, fetch_chat_history, initialize_database, save_chat
from nlp_engine import analyze_text, initialize_nltk_resources


def create_app():
    app = Flask(__name__)
    initialize_nltk_resources()
    initialize_database()

    @app.get("/")
    def index():
        return render_template("index.html")

    @app.get("/api/history")
    def history():
        return jsonify({"history": fetch_chat_history()})

    @app.delete("/api/history")
    def clear_history():
        clear_chat_history()
        return jsonify({"message": "Conversation history cleared."})

    @app.post("/api/analyze")
    def analyze():
        payload = request.get_json(silent=True) or {}
        message = payload.get("message", "").strip()
        if not message:
            return jsonify({"error": "Message is required."}), 400

        return jsonify({"analysis": analyze_text(message)})

    @app.post("/api/chat")
    def chat():
        payload = request.get_json(silent=True) or {}
        message = payload.get("message", "").strip()
        if not message:
            return jsonify({"error": "Message is required."}), 400

        analysis = analyze_text(message)
        chat_id = save_chat(message, analysis)

        return jsonify(
            {
                "id": chat_id,
                "user_message": message,
                "bot_response": analysis["response"],
                "analysis": analysis,
            }
        )

    return app


app = create_app()


if __name__ == "__main__":
    app.run(debug=True)
