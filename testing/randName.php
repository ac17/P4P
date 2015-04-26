<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<?php
function getRandomName()
{
	// url for random names
	$url = "http://www.behindthename.com/random/random.php?number=2&gender=m&surname=&all=no&usage_afr=1&usage_alb=1&usage_ara=1&usage_arm=1&usage_bas=1&usage_bre=1&usage_bul=1&usage_cat=1&usage_chi=1&usage_cor=1&usage_cro=1&usage_cze=1&usage_dan=1&usage_dut=1&usage_eng=1&usage_esp=1&usage_est=1&usage_fin=1&usage_fre=1&usage_fri=1&usage_gal=1&usage_geo=1&usage_ger=1&usage_gre=1&usage_haw=1&usage_hun=1&usage_ice=1&usage_ind=1&usage_ins=1&usage_ira=1&usage_iri=1&usage_ita=1&usage_jap=1&usage_jew=1&usage_khm=1&usage_kor=1&usage_lat=1&usage_lim=1&usage_lth=1&usage_mac=1&usage_man=1&usage_mao=1&usage_ame=1&usage_nor=1&usage_occ=1&usage_pol=1&usage_por=1&usage_rmn=1&usage_rus=1&usage_sco=1&usage_ser=1&usage_slk=1&usage_sln=1&usage_spa=1&usage_swe=1&usage_tha=1&usage_tur=1&usage_ukr=1&usage_vie=1&usage_wel=1";
	
	// create a DOM parser object
	$dom = new DOMDocument();
	
	// load the webpage 
	@$dom->loadHTMLFile($url);
	
	// array to store all the links 
	$array; 
	$i = 0; 
	
	// uterate over all the links> tags
	foreach($dom->getElementsByTagName('a') as $link) {
			# store the link
			$array[$i] = $link->nodeValue;
			++$i; 
	}
	
	// first name is element 32 and last name is element 33
	$name = $array[32] . " " .$array[33];
	
	return $name; 
}
?>
</html>