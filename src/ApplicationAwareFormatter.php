<?php

namespace PodPoint\MonologKinesis;

trait ApplicationAwareFormatter
{
    /**
     * The application name.
     *
     * @var string
     */
    public $name;

    /**
     * The application environment.
     *
     * @var string
     */
    public $environment;

    /**
     * ApplicationAwareFormatter constructor.
     *
     * @param  string  $name
     * @param  string  $environment
     */
    public function __construct(string $name, string $environment, ?string $dateFormat = null)
    {
        $this->name = $name;
        $this->environment = $environment;

        parent::__construct($dateFormat);
    }
}
