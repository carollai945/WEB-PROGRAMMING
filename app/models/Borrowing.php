<?php

class Borrowing extends Model
{
    function getAllBorrowingData()
    {
        $sql = " select d.device_name, ber.borrower_name, b.borrowing_date ,b.return_date ".
               " from borrowing as b ".
               " inner join inventories as i on i.inventory_id=b.inventory_id ".
               " inner join devices as d on i.device_id=d.device_id ".
               " inner join borrowers as ber on ber.borrower_id=b.borrower_id ".
               " order by b.borrowing_date desc " ;
        $data = $this->runQuery($sql,array());
        
        return $data;
    }
    
    function getQueryBorrowingData($keyword)
    {
        $sql = " select d.device_name, ber.borrower_name, b.borrowing_date ,b.return_date ".
               " from borrowing as b ".
               " inner join inventories as i on i.inventory_id=b.inventory_id ".
               " inner join devices as d on i.device_id=d.device_id ".
               " inner join borrowers as ber on ber.borrower_id=b.borrower_id ".
               " where (device_name like :devicekeyword or borrower_name like :borrowerkeyword  ) ".
               " order by b.borrowing_date desc " ;
        $data = $this->runQuery($sql,array(':devicekeyword'=>'%'.$keyword.'%',':borrowerkeyword'=>'%'.$keyword.'%'));
        
        return $data;
    }
    
    function getReport1()
    {
        $sql = " select d.device_name,count(distinct i.inventory_id) as total_num,sum(case when i.available=1 then 0 else 1 end) as damage_num,sum(case when bi.borrowing_id is null then 0 else 1 end) as borrowing_num ".
        " from devices as d  ".
        " inner join inventories as i on d.device_id=i.device_id ".
        " left outer join ( ".
        " select i.device_id,b.* from inventories as i ".
        " inner join borrowing as b on i.inventory_id=b.inventory_id ".
        " ) as bi on d.device_id=bi.device_id and i.inventory_id=bi.inventory_id ".
        " group by d.device_name" ;
        $data = $this->runQuery($sql,array());
        
        return $data;
        
    }
}
