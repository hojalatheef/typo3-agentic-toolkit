---
type: llm
weight: 1
---

PASS if findings carry file paths with line numbers and a severity or priority, and the agent did not modify files.
FAIL if findings have no locations, or files were edited despite the "don't change code" instruction.
