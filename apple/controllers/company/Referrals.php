<?php

class Referrals extends CI_Controller
{
    /**
     * Referrals constructor.
     */
    public function __construct()
    {
        parent::__construct();

        if (!isset($_SESSION['ses_company_data'])) {
            redirect(base_url(), 'location', 301);
        }

        $this->load->model('company_cabinet/Company_model', 'CModel');
        $this->load->model('all_areas/Category_model', 'CATModel');
        $this->load->model('all_areas/Referral_model', 'RModel');
        $this->load->library('categories');
        $this->load->library('user_agent');
    }

    public function index()
    {
        $company_data = $this->CModel->find_company_and_manager($_SESSION['ses_company_data']['deals_id']);

        $categories = $this->CATModel->get_categories($city);

        $result = $this->categories->update_array_identificator($categories);

        if ($result) {
            $category_list = $this->categories->categories_to_string($result);
        } else {
            $category_list = false;
        }

        $data = [
            'title' => 'Приглашенные компании',
            'company_data' => $company_data,
            'category_list' => $category_list,
            'reserved' => $this->CModel->reserved($_SESSION['ses_company_data']['deals_id']) ?? 0,
            'reserved_for_deals' => $this->CModel->reserved_for_deals($_SESSION['ses_company_data']['deals_id']),
            'month_sales' => $this->CModel->total_month_sales($_SESSION['ses_company_data']['deals_id']),
            'ref_link' => $this->CModel->getRefHref($_SESSION['ses_company_data']['company_id']),
        ];

        switch ($company_data['ref_mode']) {
            case 1:
                $data += ['referrals' => $this->RModel->getReferrals($company_data['company_id'])];
                break;
            case 2:
                $referrals = [];
                $all_refs = $this->RModel->getReferrals_levels($company_data['company_id'], 2);
                foreach ($all_refs as $ref) {
                    if ($ref['level'] == 0) {
                        $ref['deals_sum_ref1'] = 0;
                        $ref['deals_sum_ref2'] = 0;
                        $referrals[] = $ref;
                    }
                    else {
                        foreach ($referrals as $i => $val) {
                            if ($val['company_id'] == $ref['parent']) {
                                $referrals[$i]['deals_sum_ref' . $ref['level']] += $ref['deals_sum'];
                                break;
                            }
                        }
                    }
                }
                $total_sum = 0;
                foreach ($referrals as $i => $val) {
                    $referrals[$i]['deals_sum_ref0'] = (int)($referrals[$i]['deals_sum'] * REF_PERCENT_LV0);
                    $referrals[$i]['deals_sum_ref1'] = (int)($referrals[$i]['deals_sum_ref1'] * REF_PERCENT_LV1);
                    $referrals[$i]['deals_sum_ref2'] = (int)($referrals[$i]['deals_sum_ref2'] * REF_PERCENT_LV2);
                    $referrals[$i]['ref_sum'] = $referrals[$i]['deals_sum_ref0'] + $referrals[$i]['deals_sum_ref1'] + $referrals[$i]['deals_sum_ref2'];
                    $total_sum += $referrals[$i]['ref_sum'];
                }

                $data += [
                    'referrals' => $referrals,
                    'total_sum' => $total_sum,
                    'total_withdrawal' => $this->RModel->getTotalWidthdrawal($company_data['company_id'])
                ];

                break;
        }


        if ($this->agent->is_mobile()) {
            $this->load->view('company_area/mobile/header_view', $data );
            $this->load->view('company_area/mobile/referrals_view');
            $this->load->view('company_area/mobile/footer_view');

        } else {
            $this->load->view('company_area/header_view', $data);
            $this->load->view('company_area/referrals_view');
            $this->load->view('company_area/footer_view');
        }
    }
}