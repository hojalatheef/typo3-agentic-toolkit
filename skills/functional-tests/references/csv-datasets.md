# CSV datasets

The testing framework imports and asserts database state from CSV files. They are easier to review than PHP arrays and match the format the TYPO3 core uses.

## Format

```text
"pages"
,"uid","pid","title","slug"
,1,0,"Root","/"
,2,1,"Storage","/storage"
"tx_myext_domain_model_item"
,"uid","pid","title"
,1,2,"First"
```

- A line with only a table name starts a block.
- The next line lists the columns; it starts with an empty cell.
