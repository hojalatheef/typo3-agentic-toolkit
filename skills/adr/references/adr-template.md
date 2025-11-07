# ADR template (reStructuredText)

## Template

```rst
..  include:: /Includes.rst.txt

..  _adr-0003:

============================================================
ADR 0003: Configure the news listing through a site set
============================================================

:Status: Proposed
:Date: 2026-10-08
:Deciders: <names or roles>

Context
=======

What problem forces a decision now. Facts, constraints (TYPO3 and PHP
versions, hosting, team skills), and links to issues.

Options
=======

Option A: <name>
----------------

Short description. Pros. Cons.

Option B: <name>
----------------

Short description. Pros. Cons.

Decision
========

We choose option <X> because <reasons tied to the context>.

Consequences
============

*   Positive: ...
*   Negative: ...
*   Follow-up work: ...

References
==========

*   Site sets: Feature-103437 (TYPO3 13.1)
*   <issue, plan, documentation links>
```

## Index file

```rst
..  include:: /Includes.rst.txt

..  _decisions:

=========
Decisions
=========

..  toctree::
    :titlesonly:
    :glob:

    *
```

## Notes

- The `_adr-NNNN` label allows `:ref:` links from other pages.
- If the project has no `Documentation/Includes.rst.txt`, drop the include line; the `docs-rst` skill sets the documentation up.
