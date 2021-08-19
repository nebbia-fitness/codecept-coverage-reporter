# Codeception CRAP reporter

Codeception CRAP reporter is an open-source implementation tool to help you detect PHP code C.R.A.P. – which stands for *Change Risk Anti-Patterns*.

The current version of CRAP combines the two change risk anti-patterns we just discussed: excessive method complexity and lack of automated tests for those methods.

Given a PHP method m, CRAP for m is calculated as follows:

    CRAP(m) = comp(m)^2 * (1 – cov(m)/100)^3 + comp(m)

Where `comp(m)` is the cyclomatic complexity of method m, and `cov(m)` is the test code coverage provided by automated tests (e.g. PHPUnit tests, not manual QA). Cyclomatic complexity is a well-known and widely used metric and it’s calculated as one plus the number of unique decisions in the method. For code coverage we use basis path coverage. Low CRAP numbers indicate code with relatively low change and maintenance risk – because it’s not too complex and/or it’s well-protected by automated and repeatable tests. High CRAP numbers indicate code that’s risky to change because of a hazardous combination of high complexity and low, or no, automated test coverage.

> Generally speaking, you can lower your CRAP score either by adding automated tests or by refactoring to reduce complexity. Preferably both; and it’s a good idea to write the tests firsts so you can refactor more safely.

#### Usage

add CoverageReporter to your codeception.yml config file:
```
extensions:
    enabled:
        - \Helper\CoverageReporter
```

#### Example

![image](https://user-images.githubusercontent.com/6382002/130050189-312f2dad-e42f-411a-8c60-96f008d818c8.png)

![image](https://user-images.githubusercontent.com/6382002/130050939-e260b0e9-855b-4c36-89bd-eafa6a71143a.png)


