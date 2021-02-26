<?php

namespace App\Util;

use PhpOffice\PhpSpreadsheet\Chart\Legend;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Xslx;
use PhpOffice\PhpSpreadsheet\Reader\IReader;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Excel
{
    protected $spreadsheet;
    protected $uploadfile;

    public function __construct($uploadfile)
    {
        $reader = IOFactory::createReader('Xlsx');

        $this->spreadsheet = $reader->load($uploadfile);
    }


    /**
     * Lê dados informados na planilha
     */

    public function readerPlanProducts()
    {
        $worksheet = $this->spreadsheet->getActiveSheet();
        $highestRow = $worksheet->getHighestRow();

        $productsQuotation = [];

        for ($i = 2; $i <= $highestRow; $i++) {

            $idItem = $this->spreadsheet->getActiveSheet()->getCellByColumnAndRow(1, $i)->getValue();
            $cep = $this->spreadsheet->getActiveSheet()->getCellByColumnAndRow(2, $i)->getValue();
            $qt = $this->spreadsheet->getActiveSheet()->getCellByColumnAndRow(3, $i)->getValue();

            $productsQuotation[] = [
                'channel' => 'Estrela 10',
                'idItem' => $idItem,
                'cep' => $cep,
                'qt' => $qt
            ];
        }

        return $productsQuotation;
    }

    /**
     * Lê pedidos informados na planilha
     */

    public function readerPlanOrders()
    {
        $worksheet = $this->spreadsheet->getActiveSheet();
        $highestRow = $worksheet->getHighestRow();

        $productsQuotation = [];

        for ($i = 2; $i <= $highestRow; $i++) {

            $idPedido = $this->spreadsheet->getActiveSheet()->getCellByColumnAndRow(1, $i)->getValue();

            $productsQuotation[] = [
                'pedido' => $idPedido
            ];
        }

        return $productsQuotation;
    }

    /**
     * Grava dados na planlha
     */

    public function writePlanProducts($uploadfile, $resultQuotation)
    {
        $writer = new Xlsx($this->spreadsheet);

        for ($i = 0; $i < count($resultQuotation); $i++) {

            $linha = $i + 2;

            $Writer = $this->spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(4, $linha, $resultQuotation[$i]['protocolo']);
            $Writer = $this->spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(5, $linha, $resultQuotation[$i]['cdMicroServico']);
            $Writer = $this->spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(6, $linha, $resultQuotation[$i]['nomeTransportadora']);
            $Writer = $this->spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(7, $linha, $resultQuotation[$i]['prazo']);
            $Writer = $this->spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(8, $linha, $resultQuotation[$i]['prazoTransit']);
            $Writer = $this->spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(9, $linha, $resultQuotation[$i]['prazoExpedicao']);
            $Writer = $this->spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(10, $linha, $resultQuotation[$i]['prazoProdutoBseller']);
            $Writer = $this->spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(11, $linha, $resultQuotation[$i]['valor']);
            $Writer = $this->spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(12, $linha, $resultQuotation[$i]['custo']);
            $Writer = $this->spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(13, $linha, $resultQuotation[$i]['erro']);
        }

        $writer->save($uploadfile);
    }

    public function writePlanOrders($uploadfile, $resultQuotation)
    {
        $writer = new Xlsx($this->spreadsheet);

        for ($i = 0; $i < count($resultQuotation); $i++) {

            $linha = $i + 2;

            $Writer = $this->spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(2, $linha, $resultQuotation[$i]['protocolo']);
            $Writer = $this->spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(3, $linha, $resultQuotation[$i]['cdMicroServico']);
            $Writer = $this->spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(4, $linha, $resultQuotation[$i]['nomeTransportadora']);
            $Writer = $this->spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(5, $linha, $resultQuotation[$i]['prazo']);
            $Writer = $this->spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(6, $linha, $resultQuotation[$i]['prazoTransit']);
            $Writer = $this->spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(7, $linha, $resultQuotation[$i]['prazoExpedicao']);
            $Writer = $this->spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(8, $linha, $resultQuotation[$i]['valor']);
            $Writer = $this->spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(9, $linha, $resultQuotation[$i]['custo']);
            $Writer = $this->spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(10, $linha, $resultQuotation[$i]['erro']);
        }

        $writer->save($uploadfile);
    }
}
