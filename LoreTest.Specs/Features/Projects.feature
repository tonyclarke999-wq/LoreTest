Feature: Project Management Lifecycle
  As a QA Manager
  I want to create, configure, execute, and clean up test projects
  So that I can effectively track software quality metrics

  Scenario: Complete project lifecycle from creation to deletion
    Given the user is authenticated as an administrator
    And they navigate to the Projects page
    
    # 1. Create and Update
    When they create a new project with a unique title and description
    Then the new project should be listed on the Projects index page
    And they update the project title and description
    Then the updated details should be successfully visible on the details page

    # 2. Create Test Suite
    When they create a test suite inside the project
    Then the test suite should be visible in the project details

    # 3. Create Test Cases and Steps
    When they add two test cases, each with two test steps
    Then both test cases should be visible in the test suite details

    # 4. Execute Test Run
    When they start a test run and execute the steps with mixed pass and fail outcomes
    Then the test run pass rate should be "0%"
    And both test cases should display a "FAIL" outcome

    # 5. Cleanup
    When they delete the created test cases, suite, and project
    Then the project should no longer be listed on the Projects page
