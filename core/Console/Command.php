<?php
/**
 * Created by PhpStorm.
 * User: lixingbo
 * Date: 2019/11/11
 * Time: 下午5:46
 */

namespace Core\Console;

use Core\Helper\Parser;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\OutputStyle;
use Symfony\Component\Console\Style\SymfonyStyle;


class Command extends SymfonyCommand
{

    /**
     * @var \Core\Application
     */
    public    $app;
    protected $name;
    protected $signature;
    protected $signatures = [];
    protected $verbosity  = OutputInterface::VERBOSITY_NORMAL;

    /**
     * The mapping between human readable verbosity levels and Symfony's OutputInterface.
     *
     * @var array
     */
    protected $verbosityMap = [
        'v'      => OutputInterface::VERBOSITY_VERBOSE,
        'vv'     => OutputInterface::VERBOSITY_VERY_VERBOSE,
        'vvv'    => OutputInterface::VERBOSITY_DEBUG,
        'quiet'  => OutputInterface::VERBOSITY_QUIET,
        'normal' => OutputInterface::VERBOSITY_NORMAL,
    ];

    /**
     * The input interface implementation.
     *
     * @var \Symfony\Component\Console\Input\InputInterface
     */
    protected $input;

    /**
     * The output interface implementation.
     *
     * @var OutputStyle
     */
    protected $output;


    public function __construct(?string $name = null)
    {
        parent::__construct($name);

        $this->configureUsingFluentDefinition();
    }

    protected function configureUsingFluentDefinition()
    {
        [$name, $arguments, $options] = Parser::parse($this->signature);

        parent::__construct($this->name = $name);

        // After parsing the signature we will spin through the arguments and options
        // and set them on this command. These will already be changed into proper
        // instances of these "InputArgument" and "InputOption" Symfony classes.
        $this->getDefinition()->addArguments($arguments);
        $this->getDefinition()->addOptions($options);
    }

    public function setApp($app)
    {
        $this->app = $app;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        return $this->app->call([$this, 'handle']);
    }

    public function run(InputInterface $input, OutputInterface $output)
    {
        $this->output = new SymfonyStyle($input, $output);

        return parent::run(
            $this->input = $input, $this->output
        );
    }

    public function getName()
    {
        return $this->name;
    }

    public function info($string, $verbosity = null)
    {
        $this->line($string, 'info', $verbosity);
    }

    public function error($string, $verbosity = null)
    {
        $this->line($string, 'error', $verbosity);
    }

    public function line($string, $style = null, $verbosity = null)
    {
        $styled = $style ? "<$style>$string</$style>" : $string;

        $this->output->writeln($styled, $this->parseVerbosity($verbosity));
    }

    protected function parseVerbosity($level = null)
    {
        if (isset($this->verbosityMap[$level])) {
            $level = $this->verbosityMap[$level];
        } elseif (!is_int($level)) {
            $level = $this->verbosity;
        }

        return $level;
    }

    public function option($key = null)
    {
        if (is_null($key)) {
            return $this->input->getOptions();
        }

        return $this->input->getOption($key);
    }

    public function argument($key = null)
    {
        if (is_null($key)) {
            return $this->input->getArguments();
        }

        return $this->input->getArgument($key);
    }
}

