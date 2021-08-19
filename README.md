# Codeception CRAP reporter

Codeception CRAP reporter is an open-source implementation tool to help you detect PHP code C.R.A.P. – which stands for *Change Risk Anti-Patterns*.

The current version of CRAP combines the two change risk anti-patterns we just discussed: excessive method complexity and lack of automated tests for those methods.

Given a PHP method m, CRAP for m is calculated as follows:

    CRAP(m) = comp(m)^2 * (1 – cov(m)/100)^3 + comp(m)

Where `comp(m)` is the cyclomatic complexity of method m, and `cov(m)` is the test code coverage provided by automated tests (e.g. PHPUnit tests, not manual QA). Cyclomatic complexity is a well-known and widely used metric and it’s calculated as one plus the number of unique decisions in the method. For code coverage we use basis path coverage. Low CRAP numbers indicate code with relatively low change and maintenance risk – because it’s not too complex and/or it’s well-protected by automated and repeatable tests. High CRAP numbers indicate code that’s risky to change because of a hazardous combination of high complexity and low, or no, automated test coverage.

> Generally speaking, you can lower your CRAP score either by adding automated tests or by refactoring to reduce complexity. Preferably both; and it’s a good idea to write the tests firsts so you can refactor more safely.

### How is a crap load for a method calculated? 
 
```php
private function getCrapLoad($crapValue, $cyclomaticComplexity, $coveragePercent)
{
    $crapLoad = 0;
    if ($crapValue >= $this->threshold) {
        $crapLoad += $cyclomaticComplexity * (1.0 - $coveragePercent / 100);
        $crapLoad += $cyclomaticComplexity / $this->threshold;
    }

    return $crapLoad;
}
```

So, interpreting that, if the CRAP score for a method is above the threshold, 30, then for every point of uncovered complexity, add 1 for a test to cover that path. Then for every bit of complexity over the threshold, figure out the number of extract methods dividing in half that need to be done to get below the threshold.

ps.: more info about [CRAP](http://www.crap4j.org/faq.html)

#### Usage

1. Copy [`CoverageReporter.php`](https://github.com/nebbia-fitness/codecept-coverage-reporter/blob/master/src/CoverageReporter.php) into  `_support/Helper/` directory

2. Add CoverageReporter to your `codeception.yml` config file:
```
extensions:
    enabled:
        - \Helper\CoverageReporter
```

3. Run codecept
```
codecept run -- unit
```

#### Example

Good CRAP score:

![image](https://user-images.githubusercontent.com/6382002/130051733-5a6f18eb-012c-4ef8-96f0-1a12c5b26407.png)

Bad crap score (need more effort):

![image](https://user-images.githubusercontent.com/6382002/130051887-0317c8f0-8743-4b88-bb6c-8fd878bbdf14.png)
