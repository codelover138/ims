<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Companies_model extends CI_Model
{
    protected function tableExists($table)
    {
        return $this->db->table_exists($table);
    }

    public function __construct()
    {
        parent::__construct();
    }

    public function getAllBillerCompanies()
    {
        if (!$this->tableExists('companies')) {
            return array();
        }
        $q = $this->db->get_where('companies', array('group_name' => 'biller'));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return array();
    }

    public function getAllCustomerCompanies()
    {
        if (!$this->tableExists('companies')) {
            return array();
        }
        $q = $this->db->get_where('companies', array('group_name' => 'customer'));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return array();
    }

    public function getAllSupplierCompanies()
    {
        if (!$this->tableExists('companies')) {
            return array();
        }
        $q = $this->db->get_where('companies', array('group_name' => 'supplier'));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return array();
    }

    public function getAllCustomerGroups()
    {
        if (!$this->tableExists('customer_groups')) {
            return array();
        }
        $q = $this->db->get('customer_groups');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return array();
    }

    public function getCompanyUsers($company_id)
    {
        $q = $this->db->get_where('users', array('company_id' => $company_id));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function getCompanyByID($id)
    {
        if (!$this->tableExists('companies')) {
            return FALSE;
        }
        $q = $this->db->get_where('companies', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function getCompanyByEmail($email)
    {
        if (!$this->tableExists('companies')) {
            return FALSE;
        }
        $q = $this->db->get_where('companies', array('email' => $email), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function addCompany($data = array())
    {
        if (!$this->tableExists('companies')) {
            return false;
        }
        if ($this->db->insert('companies', $data)) {
            $cid = $this->db->insert_id();
            return $cid;
        }
        return false;
    }

    public function updateCompany($id, $data = array())
    {
        if (!$this->tableExists('companies')) {
            return false;
        }
        $this->db->where('id', $id);
        if ($this->db->update('companies', $data)) {
            return true;
        }
        return false;
    }

    public function addCompanies($data = array())
    {
        if (!$this->tableExists('companies')) {
            return false;
        }
        if ($this->db->insert_batch('companies', $data)) {
            return true;
        }
        return false;
    }

    public function deleteCustomer($id)
    {
        if (!$this->tableExists('companies')) {
            return false;
        }
        if ($this->getCustomerSales($id)) {
            return false;
        }
        if ($this->db->delete('companies', array('id' => $id, 'group_name' => 'customer')) && $this->db->delete('users', array('company_id' => $id))) {
            return true;
        }
        return FALSE;
    }

    public function deleteSupplier($id)
    {
        if (!$this->tableExists('companies')) {
            return false;
        }
        if ($this->getSupplierPurchases($id)) {
            return false;
        }
        if ($this->db->delete('companies', array('id' => $id, 'group_name' => 'supplier')) && $this->db->delete('users', array('company_id' => $id))) {
            return true;
        }
        return FALSE;
    }

    public function deleteBiller($id)
    {
        if (!$this->tableExists('companies')) {
            return false;
        }
        if ($this->getBillerSales($id)) {
            return false;
        }
        if ($this->db->delete('companies', array('id' => $id, 'group_name' => 'biller'))) {
            return true;
        }
        return FALSE;
    }

    public function getBillerSuggestions($term, $limit = 10)
    {
        if (!$this->tableExists('companies')) {
            return array();
        }
        $this->db->select("id, company as text");
        $this->db->where(" (id LIKE '%" . $term . "%' OR name LIKE '%" . $term . "%' OR company LIKE '%" . $term . "%') ");
        $q = $this->db->get_where('companies', array('group_name' => 'biller'), $limit);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }

            return $data;
        }
    }

    public function getCustomerSuggestions($term, $limit = 10)
    {
        if (!$this->tableExists('companies')) {
            return array();
        }
        $user = $this->getUser();
        $warehouse_id = $user->warehouse_id;
        if((!$this->Owner && !$this->Admin) && $warehouse_id)
        {
        $this->db->where("warehouse_id", $warehouse_id);

       }
        $this->db->select("id, (CASE WHEN company = '-))' THEN name ELSE  CONCAT(name,' ',last_name) END) as text, (CASE WHEN company = '-' THEN name ELSE CONCAT(company, ' (', name, ')') END) as value, phone", FALSE);
        $this->db->where(" (id LIKE '%" . $term . "%' OR name LIKE '%" . $term . "%' OR company LIKE '%" . $term . "%' OR email LIKE '%". $term . "%' OR vat_no LIKE '%" . $term . "%' OR phone LIKE '%" . $term . "%') ");
        $q = $this->db->get_where('companies', array('group_name' => 'customer'), $limit);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }

            return $data;
        }
    }

      public function getUser($id = NULL) {
        if (!$id) {
            $id = $this->session->userdata('user_id');
        }
        if($id) 
        $q = $this->db->get_where('users', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function getSupplierSuggestions($term, $limit = 10)
    {
        if (!$this->tableExists('companies')) {
            return array();
        }
        $this->db->select("id, (CASE WHEN company = '-' THEN name ELSE CONCAT(company, ' (', name, ')') END) as text", FALSE);
        $this->db->where(" (id LIKE '%" . $term . "%' OR name LIKE '%" . $term . "%' OR company LIKE '%" . $term . "%' OR email LIKE '%" . $term . "%' OR phone LIKE '%" . $term . "%') ");
        $q = $this->db->get_where('companies', array('group_name' => 'supplier'), $limit);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }

            return $data;
        }
    }

    public function getCustomerSales($id)
    {
        if (!$this->tableExists('sales')) {
            return 0;
        }
        $this->db->where('customer_id', $id)->from('sales');
        return $this->db->count_all_results();
    }

    public function getBillerSales($id)
    {
        if (!$this->tableExists('sales')) {
            return 0;
        }
        $this->db->where('biller_id', $id)->from('sales');
        return $this->db->count_all_results();
    }

    public function getSupplierPurchases($id)
    {
        if (!$this->tableExists('purchases')) {
            return 0;
        }
        $this->db->where('supplier_id', $id)->from('purchases');
        return $this->db->count_all_results();
    }

    public function addDeposit($data, $cdata)
    {
        if ($this->db->insert('deposits', $data) &&
            $this->db->update('companies', $cdata, array('id' => $data['company_id']))) {
            return true;
        }
        return false;
    }

    public function updateDeposit($id, $data, $cdata)
    {
        if ($this->db->update('deposits', $data, array('id' => $id)) &&
            $this->db->update('companies', $cdata, array('id' => $data['company_id']))) {
            return true;
        }
        return false;
    }

    public function getDepositByID($id)
    {
        $q = $this->db->get_where('deposits', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function deleteDeposit($id)
    {
        $deposit = $this->getDepositByID($id);
        $company = $this->getCompanyByID($deposit->company_id);
        $cdata = array(
                'deposit_amount' => ($company->deposit_amount-$deposit->amount)
            );
        if ($this->db->update('companies', $cdata, array('id' => $deposit->company_id)) &&
            $this->db->delete('deposits', array('id' => $id))) {
            return true;
        }
        return false;
    }

    public function getAllPriceGroups()
    {
        if (!$this->tableExists('price_groups')) {
            return array();
        }
        $q = $this->db->get('price_groups');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return array();
    }

    public function getCompanyAddresses($company_id)
    {
        $q = $this->db->get_where('addresses', array('company_id' => $company_id));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function addAddress($data)
    {
        if ($this->db->insert('addresses', $data)) {
            return true;
        }
        return false;
    }

    public function updateAddress($id, $data)
    {
        if ($this->db->update('addresses', $data, array('id' => $id))) {
            return true;
        }
        return false;
    }

    public function deleteAddress($id)
    {
        if ($this->db->delete('addresses', array('id' => $id))) {
            return true;
        }
        return false;
    }

    public function getAddressByID($id)
    {
        $q = $this->db->get_where('addresses', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

}
