<?php


namespace Barter;


class CompanyDeals
{
    private $db;
    /**
     * @var string
     */
    private $deal_id;

    public function __construct(string $deal_id)
    {
        $this->db = get_instance()->db;
        $this->deal_id = $deal_id;
    }

    public function count()
    {
        return $this->db->count_all_results();
    }

    public function withCompany(string $joinOn = 'buyer_deal_id'): self
    {
        $this->db->select('c.company_id,
                        c.company_name,
                        c.logo,
                        c.city_name,
                        d.deal_id,
                        d.date,
                        d.summa_sdelki,
                        d.status_deal,
                        d.seller_deal_id,
                        d.buyer_deal_id,
                        d.comment_deal,
                        d.want_cancel')
            ->join('companies c', "d.{$joinOn} = c.for_deals_id");
        return $this;
    }

    public function outboxing(): self
    {
        $this->db->where('d.buyer_deal_id', $this->deal_id)
            ->from('deals d');
        return $this;
    }

    public function unaccepted(): self
    {
        $this->db->where('d.status_deal', 1);
        return $this;
    }

    public function incoming()
    {
        $this->db->where('d.seller_deal_id', $this->deal_id)
            ->from('deals d');
        return $this;
    }

    public function all()
    {
        $this->db->where('d.seller_deal_id', $this->deal_id)
            ->or_where('d.buyer_deal_id', $this->deal_id)
            ->from('deals d');
        return $this;
    }

    public function allWithCompany(): self
    {
        $this->withCompany();
        $this->db->select('c1.company_id as s_company_id,
                        c1.company_name s_company_name,
                        c1.logo s_logo,
                        c1.city_name s_city')
            ->where('d.seller_deal_id', $this->deal_id)
            ->or_where('d.buyer_deal_id', $this->deal_id)
            ->join('companies c1', 'd.seller_deal_id = c1.for_deals_id')
            ->from('deals d');
        return $this;
    }

    public function paginate(int $perPage = null, int $offset = null)
    {
        $this->db->limit($perPage, $offset)->order_by('d.date', 'desc');
        return $this;
    }

    public function get()
    {
        $this->db->select('coup.summa as coupon_sum')
            ->join('coupons coup', 'd.deal_id = coup.deal_id', 'left');
        $res = $this->db->get();
        if ($res) {
            $arr = [];
            $data = $res->result_array();
            foreach ($data as $datum) {
                $date = Date::parse($datum['date']);
                $arr[$date][] = $datum;
            }
            return $arr;

        }
        return [];
    }

}
