---
type: llm
weight: 2
---

PASS if the analysis classifies the ticket as a bug, lists stated acceptance criteria (mail is sent again; footer keeps page id and username) separately from derived ones marked as derived, proposes a failing test to write first, and names a size (S/M/L) and a next step.
FAIL if stated and derived criteria are mixed without marking, or there is no test proposal or next step.
