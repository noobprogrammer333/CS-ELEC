import json
import subprocess
import sys
import unittest
from pathlib import Path


PROCESSOR = Path(__file__).resolve().parents[1] / "nlp" / "nlp_processor.py"


class NlpProcessorTest(unittest.TestCase):
    def analyze(self, message):
        result = subprocess.run(
            [sys.executable, str(PROCESSOR)],
            input=json.dumps({"message": message}),
            text=True,
            capture_output=True,
            check=True,
        )
        return json.loads(result.stdout)

    def test_positive_feedback_analysis(self):
        analysis = self.analyze("I love this helpful NLP chatbot")

        self.assertEqual(analysis["sentiment"], "Positive")
        self.assertEqual(analysis["classification"], "Feedback")
        self.assertIn("NLTK", analysis["nlp_tools"])
        self.assertIn("spaCy", analysis["nlp_tools"])
        self.assertTrue(analysis["keywords"])
        self.assertTrue(analysis["lemmas"])

    def test_question_classification(self):
        analysis = self.analyze("How does sentiment analysis work?")

        self.assertEqual(analysis["classification"], "Inquiry")
        self.assertIn("How", analysis["tokens"])


if __name__ == "__main__":
    unittest.main()
