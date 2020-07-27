Feature: Developer enables laravel extension
  As a Developer
  I want to enable and configure Laravel Extension
  In order to test laravel application or package

  Background:
    Given a file named "features/bootstrap/FeatureContext.php" with:
      """
      <?php

      use Behat\Behat\Context\Context;

      class FeatureContext implements Context
      {
      }
      """
    And a file named "features/config.feature" with:
      """
      Feature:
        Scenario:
          When this scenario executes
      """
  Scenario: Using default configuration
    Given a file named "behat.yml" with:
      """
      default:
        extensions:
          Behat\LaravelExtension:
            type: package
            providers:
              - Tests\DummyPackage\DummyServiceProvider
            aliases:
              SomeAliases: Some\Aliases
      """
    When I run "behat -f progress --no-colors --config-reference"
    Then it should pass
    And the output should contain:
      """
      laravel:
      """
    And the output should contain:
      """
      # list your package providers
      """
    And the output should contain:
      """
      # list your package aliases
      """