<?php

namespace Rizeway\Anchour\Step\Steps;

use Rizeway\Anchour\Step\Step;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Console\Output\OutputInterface;

class StepGit extends StepSsh
{
    public function __construct(array $options = array(), $connections = array(), 
        OptionsResolverInterface $options_resolver = null, OptionsResolverInterface $connections_resolver = null, AdapterInterface $adapter = null)
    {
        $output = $status = null;
        exec('which git', $output, $status);

        if(0 !== $status)
        {
            throw new \RuntimeException('git command is not available');
        }

        parent::__construct($options, $connections, $options_resolver, $connections_resolver, $adapter);
    }

    protected function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        parent::setDefaultOptions($resolver);

        $resolver->setRequired(array(
            'repository',
            'remote_dir',
        ));

        $resolver->setDefaults(array(
            'clean_scm' => true,
            'remove_existing' => false,
            'commands' => array()
        ));
    }

    public function run(OutputInterface $output)
    {
        if (true === $this->options['remove_existing'])
        {
            $this->options['commands'][] = sprintf('rm -rf %s', $this->options['remote_dir']);
        }

        $this->options['commands'][] = sprintf('git clone %s %s', $this->options['repository'], $this->options['remote_dir']);

        if (true === $this->options['clean_scm'])
        {
            $this->options['commands'][] = sprintf('rm -rf %s/.git', $this->options['remote_dir']);
        }

        parent::run($output);
    }
}