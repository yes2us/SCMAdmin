<?php
namespace Home\Controller;

class WBAttendanceMngController extends \Think\Controller {

		public function queryAttendanceRecord(){
			$fieldstr = "StaffAttendance._Identify,RecordTime,c.DeptCode,DeptSName,StaffAttendance.StaffCode,a.StaffName,";
			$fieldstr = $fieldstr . "AttendanceType,OverTimeHours,OffDays,AbsentDays,Remark,AuditDate,AuditorCode,b.StaffName AuditorName,AuditState,";
			$fieldstr = $fieldstr . "(case when AuditState='批准' then 1 else case when AuditState='拒绝' then 0 else 2 end end) AuditValue";

			$condition = getInputValue('Condition');
			
			if($condition)
			{
				$rs = M('staffattendance',"",getMyCon()) 
				->join("left join Staffs as a on a.StaffCode = StaffAttendance.StaffCode")
				->join("left join Staffs as b on b.StaffCode = StaffAttendance.AuditorCode")
				->join("left join Depts  as c on c.DeptCode = a.BelongDeptCode")
				-> where($condition)
				->page("1,5000")
				->field($fieldstr)
				->order("recordtime desc")
				->select();
			}
			else
			{
				$rs = M('staffattendance',"",getMyCon()) 
				->join("left join Staffs as a on a.StaffCode = StaffAttendance.StaffCode")
				->join("left join Staffs as b on b.StaffCode = StaffAttendance.AuditorCode")
				->join("left join Depts  as c on c.DeptCode = a.BelongDeptCode")
				->page("1,5000")
				->field($fieldstr)
				->order("recordtime desc")
				->select();
			}
			return $this -> ajaxReturn($rs);
		}
}
?>