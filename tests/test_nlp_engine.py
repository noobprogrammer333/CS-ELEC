import unittest

from nlp_engine import analyze_text


class NlpEngineTest(unittest.TestCase):
    def test_positive_feedback_analysis(self):
        analysis = analyze_text("I love this helpful NLP chatbot")

        self.assertIn("love", analysis["normalized_tokens"])
        self.assertEqual(analysis["sentiment"], "Positive")
        self.assertIn("Feedback", analysis["classification"])
        self.assertTrue(analysis["keywords"])
        self.assertTrue(analysis["response"])

    def test_question_classification(self):
        analysis = analyze_text("How does sentiment analysis work?")

        self.assertEqual(analysis["classification"], "Inquiry")
        self.assertIn("How", analysis["tokens"])


if __name__ == "__main__":
    unittest.main()
