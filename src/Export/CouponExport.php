<?php

namespace App\Export;

use DataTable\Core\Table;
use Symfony\Component\HttpFoundation\Response;
use DataTable\Core\Writer\Csv as CsvWriter;

class CouponExport
{
    protected $coupons;

    public function __construct($coupons)
    {
        $this->coupons = $coupons;
    }

    public function export()
    {
        $couponArray = [];

        foreach ($this->coupons as $coupon) {
            $couponArray[] = [
                'xuid' => $coupon->getXuid(),
                'code' => $coupon->getCode(),
                'username' => $coupon->getUser(),
                'createdAt' => date('Y-m-d H:i:s', $coupon->getCreatedAt()),
                'usedAt' => ($coupon->getUsedAt()) ? date('Y-m-d H:i:s', $coupon->getUsedAt()) : '',
            ];
        }

        return $couponArray;
    }

    public function exportCsv()
    {
        $dataArray = $this->export();
        $output = '';

        $table = new Table();
        $table->setName(basename('coupon.csv'));

        $i = 0;
        foreach ($dataArray as $data) {
            $row = $table->getRowByIndex($i);

            foreach ($data as $key => $value) {
                if (!$i) {
                    $col = $table->getColumnByName($key);
                }
                // set value
                $col = $row->getCellByColumnName($key);
                $col->setValue($value);
            }
            ++$i;
        }

        $writer = new CsvWriter();
        $writer->setEnclosure('');
        $writer->setSeperator("\t");

        return $output = $writer->write($table);
    }

    public function getResponse()
    {
        // custom code //
        $output = $this->exportCsv();
        $response = new Response($output);
        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Disposition', 'attachment; filename="coupon.csv"');
        $response->headers->set('Content-Transfer-Encoding', 'binary');

        return $response;
    }
}
