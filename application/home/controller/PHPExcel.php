<?php
/**
 * Thinkphp整合PHPExcel
 */
namespace app\home\controller;
use think\Controller;
use think\Loader;

class PHPExcel extends Base {
	/**
	 * [read 读取表格数据]
	 * @param  [type] $savepath [文件目录]
	 * @param  [type] $savename [文件名称]
	 * @param  [type] $Sheet    [工作表]	获取表中工作表，如果要获取第一个，为0，依次类推
	 * @param  [type] $Row      [行]	从哪行开始读取数据，索引值从0开始
	 * @param  [type] $Column   [列]	//从哪列开始，A表示第一列	依次类推
	 */
	public function read($savepath, $savename, $Sheet = 0, $Row = 0, $Column = 'A') {
		$filename = $savepath . $savename;
		if (!file_exists($filename)) {
			$return = array('status' => 0, 'info' => '文件不存在!');
		} else {
			//import("PHPExcel.PHPExcel"); //导入PHPExcel类库，因为PHPExcel没有用命名空间，只能inport导入
			//$PHPExcel = new \PHPExcel(); //创建PHPExcel对象，注意，不能少了\
			Loader::import('PHPExcel.PHPExcel');
	        $PHPExcel=new \PHPExcel();


			// 判断后缀名称
			$ext = $this->_getEtc($savename);
			if ($ext == 'xls') {
				//如果excel文件后缀名为.xls，导入这个类
				Loader::import('PHPExcel.PHPExcel.Reader.Excel5');
				$PHPReader = new \PHPExcel_Reader_Excel5();
			} else if ($ext == 'xlsx') {
				//如果excel文件后缀名为.xlsx，导入这下类
				Loader::import('PHPExcel.PHPExcel.Reader.Excel2007');
				$PHPReader = new \PHPExcel_Reader_Excel2007();
			}
			$PHPExcel = $PHPReader->load($filename); //载入文件
			$currentSheet = $PHPExcel->getSheet($Sheet); //获取表中的第一个工作表，如果要获取第二个，把0改为1，依次类推
			$allColumn = $currentSheet->getHighestColumn(); //获取总列数
			$allRow = $currentSheet->getHighestRow(); //获取总行数

			//循环获取表中的数据，$currentRow表示当前行，从哪行开始读取数据，索引值从0开始
			for ($currentRow = $Row; $currentRow <= $allRow; $currentRow++) {
				//从哪列开始，A表示第一列
				for ($currentColumn = $Column; $currentColumn <= $allColumn; $currentColumn++) {
					$address = $currentColumn . $currentRow; //数据坐标
					//读取到的数据，保存到数组$arr中
					$arr[$currentRow][$currentColumn] = $currentSheet->getCell($address)->getValue();
				}
			}
			$return = array('status' => 1, 'info' => $arr);
		}
		return $return;
	}

	/**
	 * [_getEtc 判断后缀名称]
	 */
	private function _getEtc($savename) {
		return substr(strrchr($savename, '.'), 1);
	}

	public function write($expTitle, $expCellName, $expTableData) {
		//导入PHPExcel类库，因为PHPExcel没有用命名空间，只能inport导入
		Loader::import('PHPExcel.PHPExcel');
		Loader::import('PHPExcel.PHPExcel.Writer.Excel5');
		Loader::import('PHPExcel.PHPExcel.IOFactory.php');
		//对数据进行检验
		if (empty($expTableData) || !is_array($expTableData)) {
			die("data must be a array");
		}
		//检查文件名
		if (empty($expTitle)) {
			exit;
		}

		$filename = $expTitle . '_' . date('YmdHis');
		//创建PHPExcel对象，注意，不能少了\
		$objPHPExcel = new \PHPExcel();
		$objProps = $objPHPExcel->getProperties();

		//设置表头
		$key = ord("A");
		foreach ($expCellName as $v) {
			$colum = chr($key);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue($colum . '1', $v);
			$key += 1;
		}

		$column = 2;
		$objActSheet = $objPHPExcel->getActiveSheet();
		foreach ($expTableData as $key => $rows) {
			//行写入
			$span = ord("A");
			foreach ($rows as $keyName => $value) {
				// 列写入
				$j = chr($span);
				$objActSheet->setCellValue($j . $column, $value);
				$span++;
			}
			$column++;
		}

		$fileName = iconv("utf-8", "gb2312", $filename);
		//设置活动单指数到第一个表,所以Excel打开这是第一个表
		$objPHPExcel->setActiveSheetIndex(0);
		header('Content-Type: application/vnd.ms-excel');
		header("Content-Disposition: attachment;filename=$fileName.xls");
		header('Cache-Control: max-age=0');

		$objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		$objWriter->save('php://output'); //文件通过浏览器下载
		exit;
	}
}
?>