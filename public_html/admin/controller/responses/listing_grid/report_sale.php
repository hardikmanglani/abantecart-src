<?php
/*------------------------------------------------------------------------------
  $Id$

  AbanteCart, Ideal OpenSource Ecommerce Solution
  http://www.AbanteCart.com

  Copyright © 2011 Belavier Commerce LLC

  This source file is subject to Open Software License (OSL 3.0)
  License details is bundled with this package in the file LICENSE.txt.
  It is also available at this URL:
  <http://www.opensource.org/licenses/OSL-3.0>

 UPGRADE NOTE:
   Do not edit or add to this file if you wish to upgrade AbanteCart to newer
   versions in the future. If you wish to customize AbanteCart for your
   needs please refer to http://www.AbanteCart.com for more information.
------------------------------------------------------------------------------*/
if (! defined ( 'DIR_CORE' ) || !IS_ADMIN) {
	header ( 'Location: static_pages/' );
}
class ControllerResponsesListingGridReportSale extends AController {
	private $error = array();

    public function main() {

	    //init controller data
        $this->extensions->hk_InitData($this,__FUNCTION__);

		$this->loadLanguage('report/sale');
		$this->loadModel('report/sale');

		$page = $this->request->post['page']; // get the requested page
		$limit = $this->request->post['rows']; // get how many rows we want to have into the grid
		$sidx = $this->request->post['sidx']; // get index row - i.e. user click to sort
		$sord = $this->request->post['sord']; // get the direction



	    $data = array(
			'sort'  => $sidx,
			'order' => $sord,
			'start' => ($page - 1) * $limit,
			'limit' => $limit
		);

	            //process custom search form
	    $allowedSearchFilter = array('date_start', 'date_end', 'group');
        foreach ( $allowedSearchFilter as $filter) {
            if ( isset($this->request->get[$filter]) && $this->request->get[$filter] != '' ) {
                $data[$filter] = $this->request->get[$filter];
            }
        }

		$total = $this->model_report_sale->getSaleReportTotal($data);
	    if( $total > 0 ) {
			$total_pages = ceil($total/$limit);
		} else {
			$total_pages = 0;
		}

	    $response = new stdClass();
		$response->page = $page;
		$response->total = $total_pages;
		$response->records = $total;

	    $results = $this->model_report_sale->getSaleReport($data);
	    $i = 0;
		foreach ($results as $result) {

            $response->rows[$i]['id'] = $i;
			$response->rows[$i]['cell'] = array(
				dateISO2Display($result['date_start'], $this->language->get('date_format_short')),
				dateISO2Display($result['date_end'], $this->language->get('date_format_short')),
				$result['orders'],
                $this->currency->format($result['total'], $this->config->get('config_currency')),
			);
			$i++;
		}

		//update controller data
        $this->extensions->hk_UpdateData($this,__FUNCTION__);

		$this->load->library('json');
		$this->response->setOutput(AJson::encode($response));
	}

}