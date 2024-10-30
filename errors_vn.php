<?php
		$list_errors_vn=array (
			'401' => 'Kết nối thất bại ! Vui lòng kiểm tra khóa API và khóa bí mật !' ,
	    	'441' => 'Tài khoản vẫn chưa được kích hoạt ! Vui lòng liên hệ bên cung cấp để được kích hoạt!'
	    );

    function errors_param($code,$par1,$par2){
		$list_errors_vn_param = array (
			'1002' => 'Thuê bao '.$par1.' không được tìm thấy !',
			'1023' => 'Số lượng cộng tác viên của hệ thống đã đạt giới hạn '.$par1,
			'52'   => 'Không thể tìm thấy người giới thiệu '.$par1,
			'56'   => 'Người giới thiệu '.$par1.' không thuộc thuê báo '.$par2,
			'51'   => 'Nickname '.$par1 . ' đã đăng ký một chức vụ trong hệ thống !',
			'1051' => 'Email này '.$par1 . ' đã được đăng ký !',
			'53'   => 'Công tác viên '.$par1.' đã đăng ký chỉ định tại thuê bao '.$par2,
			'500'  => 'Kết nối hệ thống máy chủ YBAI thất bại ! Vui lòng liên hệ người quản trị !',
			'1052' => 'Tài khoản '.$par1.' không được tìm thấy !',
			'1053' => 'Tài khoản '.$par1.' chưa được kích hoạt !',
			'54'   => 'Cộng tác viên '.$par1.' không được tìm thấy !',
			'1028' => 'Thuê bao '. $par1. ' không có thống kê chia sẻ !',
			'1022' => 'Thuê bao đã hết hạn sử dụng vào lúc '.$par1,
			'302'  => 'Kênh bán hàng ' .$par1.' không được tìm thấy !',
			'451'  => 'Mã số đơn hàng '.$par1.' đã tồn tại trong kênh bán hàng'.$par2,
			'356'  => 'Mã giảm giá '.$par1.' của Cộng tác viên '.$par2.' không được tìm thấy !',
			'452'  => 'Cộng tác viên và kênh bán hàng không tồn tại chung thuê bao !',
			'101'  => 'Tài khoản đã đăng ký '.$par1.' không thuộc về thuê bao'.$par2
		);
		if ($list_errors_vn_param[$code]==null) return 'Thao tác thất bại ! Vui lòng liên hệ với quản trị để xử lý !';
		return $list_errors_vn_param[$code];
    }
?>