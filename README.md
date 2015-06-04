# ConclurerGenderApi

Allows integrated access to gender-api.com's database.

## How it works

ConclurerGenderApi accesses gender-api.com returning the gender of a given first name. The result can be optionally influenced by the origin of the first name.

## Methods

ConclurerGenderApi delivers a set of methods to adjust the request.


| Method | Description |
| -- | -- |
| `name($s)` | Sets the name to be detected |
| `names(array $r)` | Sets multiple names at once to be accessed in a single call. Every element in the array will be treated as indivudal name. Maximum: 100 names per call. |
| `email($s)` | Sets the email address to be detected for gender| 
| `emailAddress($s)` | Alias for `email($s)` |
| `ipAddress($s)` | Uses an given ip address to detect the gender based on the origin of the given IP address. |
| `currentIpAddress()` | Uses the current `$_SERVER["REMOTE_ADDR"]` to detect the gender based on the origin of the current IP address. |
| `language($s)` | Uses a given browser locale string to detect the gender based on the given language. |
| `country($s)` | Uses a two digit country string (e.g. "DE" or "US") to detect the gender of the name in the context of the given country. |
| `fetch()` | Fetches the results from gender-api.com, returning a `ConclurerGenderApiResult` object on success or a `ConclurerGenderApiErrorResult` object on failure. If multiple names have been requested, a `WireArray` is returned on success. |


### Accessing the Results

The results can be accessed from the ConclurerGenderApiResult objects.

Example:

```php
$gender = $modules->get("ConclurerGenderApi");
$result = $gender->name("Marvin")->fetch();

echo $result->gender; // => male
```

The full list of return attributes can be accessed on [https://gender-api.com/en/api-docs](https://gender-api.com/en/api-docs).

### Detecting Errors

To check if an error occurred, use the following statement

```php
if ($result instanceof ConclurerGenderApiErrorResult) {
	// error occurred
}
```

If the gender could not be detected, `$result->gender` will return `unknown`.

## Examples

```php
$gender = $modules->get("ConclurerGenderApi");

// Detect gender of name "Marvin" worldwide
$result = $gender->name("Marvin")->fetch();

// Detect gender of name "Philipp" within Germany
$result2 = $gender->name("Philipp")->country("DE")->fetch();
 
// Detect genders of names "Marvin", "Philipp" and "Thomas" within Germany
$nameArray = array("Marvin", "Philipp", "Thomas");
$result3 = $gender->names($nameArray)->fetch();

// Detect gender of name "Valentin" using the current user's IP address
$result4 = $gender->name("Valentin")->currentIpAddress()->fetch();

// Detect gender using a email address
$result5 = $gender->email("marvin.scharle@conclurer.com")->fetch();
```