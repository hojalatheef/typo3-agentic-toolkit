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
- Each data row also starts with an empty cell.
- Several tables can live in one file.
- Quote strings that contain commas or quotes; double a quote inside a quoted string.

## Import and assert

| Method | Purpose |
| --- | --- |
| `$this->importCSVDataSet($path)` | insert the rows before the test |
| `$this->assertCSVDataSet($path)` | compare the listed tables and columns with the database |

`assertCSVDataSet()` only checks the columns present in the file, so keep expected files narrow: `uid`, `pid`, the fields the test changes, and `deleted`/`hidden` when relevant. Rows missing from the file but present in the database make the assertion fail, which catches unwanted inserts.

## Organisation

```text
Tests/Functional/
  Fixtures/
    pages.csv                 shared base
    Expected/
      AddPersistsRecord.csv   one per assertion, named after the test
```

- Share a base dataset; put per-test variations into small extra files imported after it.
- Name expected files after the test method.
