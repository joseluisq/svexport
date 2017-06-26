# SVExport

> Simple PHP character-separated values exporter (CSV and TSV).

## Usage

```php
<?php

$data_source = array(
  array('19880920', 'John', 'Doe', 'john.doe@mail.com', 'Lima', 'Perú')
);

$export = new SVExport();
$export->toTSV();
$export->fromArray($data_source);
$export->output();
```
## License
MIT license

© 2016 [José Luis Quintana](http://git.io/joseluisq)
