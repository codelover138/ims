<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Db_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
    }

    public function getLatestSales()
    {
        // Removed: sales table dropped
        return array();
    }

    public function getLastestQuotes()
    {
        // Removed: quotes table dropped
        return array();
    }

    public function getLatestPurchases()
    {
        // Removed: purchases table dropped
        return array();
    }

    public function getLatestTransfers()
    {
        // Removed: transfers table dropped
        return array();
    }

    public function getLatestCustomers()
    {
        // Removed: companies table dropped
        return array();
    }

    public function getLatestSuppliers()
    {
        // Removed: companies table dropped
        return array();
    }

    public function getChartData()
    {
        // Removed: sales and purchases tables dropped
        return array();
    }

    public function getStockValue()
    {
        // Removed: products and warehouses_products tables dropped
        return FALSE;
    }

    public function getBestSeller($start_date = NULL, $end_date = NULL)
    {
        // Removed: sale_items and sales tables dropped
        return FALSE;
    }

}
