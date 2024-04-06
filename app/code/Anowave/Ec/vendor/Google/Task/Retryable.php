<?php
namespace Anowave\Ec\vendor\Google\Task;

interface Retryable
{
  /**
   * Gets the number of times the associated task can be retried.
   *
   * NOTE: -1 is returned if the task can be retried indefinitely
   *
   * @return integer
   */
  	public function allowedRetries();
}