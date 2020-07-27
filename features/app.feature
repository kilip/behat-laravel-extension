Feature: Developer enables laravel extension
  As a Developer
  I want to enable and configure Laravel Extension
  In order to test laravel application

  Background:
    Given I am in laravel project directory
    And there is a file named "features/bootstrap/FeatureContext.php" with:
      """
      <?php

      use Behat\Behat\Context\Context;

      class FeatureContext implements Context
      {
      }

      """
    And a file named "behat.yml" with:
      """
      default:
        suites:
          default:
            contexts:
              - FeatureContext
              - Behat\MinkExtension\Context\MinkContext
        extensions:
          Behat\LaravelExtension:
            environment:
              foo: "bar"
            providers:
              - Tests\DummyPackage\DummyServiceProvider
          Behat\MinkExtension:
            sessions:
              default:
                laravel: ~
            base_url: 'http://localhost/'
      """
    And a file named "features/homepage.feature" with:
    """
    Feature: Homepage
      Scenario: Visit homepage
        Given I am on the homepage
        Then the response status code should be 200
        And the response should contain "Laravel"
    """

  Scenario: Should pass loading
    When I run "behat -f progress -vvv --no-colors --no-interaction"
    Then it should pass
    And the output should contain:
    """
    1 scenario (1 passed)
    3 steps (3 passed)
    """