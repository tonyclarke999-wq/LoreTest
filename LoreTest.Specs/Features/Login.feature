Feature: Login
  As an administrator
  I want to successfully authenticate
  So that I can access my personalized dashboard

  Scenario: Home Page loads successfully after admin authentication
    Given the user navigates to the login page
    When they sign in with administrator credentials
    And they navigate to the home page
    Then the dashboard title should be displayed
