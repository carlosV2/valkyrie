Feature: Making request
  In order to test my application
  As a Valkyrie user
  I need to be able to make requests

  Scenario: Make request to the API
    Given there is a file containing:
      """
          {
              "tests": [
                  {
                      "endpoint": "http://valkyrie.apiary-mock.com/notes",
                      "status_code": 200
                  }
              ]
          }
      """
    When I run "valkyrie"
    Then it should pass