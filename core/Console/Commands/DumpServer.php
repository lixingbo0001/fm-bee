<?php
/**
 * Created by PhpStorm.
 * User: lixingbo
 * Date: 2019/11/26
 * Time: 下午12:05
 */

namespace Core\Console\Commands;


use Core\Console\Command;
use Symfony\Component\VarDumper\Cloner\Data;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\VarDumper\Dumper\CliDumper;
use Symfony\Component\VarDumper\Dumper\HtmlDumper;
use Symfony\Component\VarDumper\Server\DumpServer as Server;
use Symfony\Component\VarDumper\Command\Descriptor\CliDescriptor;
use Symfony\Component\VarDumper\Command\Descriptor\HtmlDescriptor;

class DumpServer extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'dump-server {--format=cli : The output format (cli,html).}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Start the dump server to collect dump information.';

    protected $server;


    public function __construct()
    {
        $this->server = new Server(config('app.dump.host'));

        parent::__construct();
    }

    public function handle()
    {
        switch ($format = $this->option('format')) {
            case 'html':
                $descriptor = new HtmlDescriptor(new HtmlDumper);
                break;
            default:
                $descriptor = new CliDescriptor(new CliDumper);
        }

        $io = new SymfonyStyle($this->input, $this->output);

        $errorIo = $io->getErrorStyle();
        $errorIo->title('Var Dump Server');

        $this->server->start();

        $errorIo->success(sprintf('Server listening on %s', $this->server->getHost()));
        $errorIo->comment('Quit the server with CONTROL-C.');

        $this->server->listen(function (Data $data, array $context, int $clientId) use ($descriptor, $io) {
            $descriptor->describe($io, $data, $context, $clientId);
        });
    }
}
