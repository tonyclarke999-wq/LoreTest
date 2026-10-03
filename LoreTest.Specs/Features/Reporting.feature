Feature: Reporting Dashboard and Grouped QA Metrics
  As a QA Manager
  I want to view consolidated reports for projects, test suites, test cases, test runs, and bugs
  So that I can effectively monitor testing progress, grouping, and defects with advanced filtering

  Scenario: Navigating the reporting dashboard and validating all reports and filters
    Given the user is authenticated as an administrator
    And they navigate to the Reporting page
    Then they should see the Reporting dashboard with active metrics summary cards

    # 1. Projects Report
    When they switch to the "Projects" reporting tab
    Then they should see a list of all projects
    And they can filter projects by a search query "Specs"

    # 2. Test Suites Grouped by Project
    When they switch to the "TestSuites" reporting tab
    Then they should see test suites grouped by project
    And they can filter test suites by selecting a project

    # 3. Test Cases Grouped by Test Suite
    When they switch to the "TestCases" reporting tab
    Then they should see test cases grouped by test suite
    And they can filter test cases by priority "High"

    # 4. Test Runs Grouped by Test Case
    When they switch to the "TestRuns" reporting tab
    Then they should see test runs grouped by test case
    And they can filter test runs by status "Passed"

    # 5. Bugs Report
    When they switch to the "Bugs" reporting tab
    Then they should see a list of all bugs with filters
    And they can filter bugs by severity "Critical"
