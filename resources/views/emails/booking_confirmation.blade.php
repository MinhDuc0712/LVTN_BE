<div style="font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; max-width: 640px; margin: 0 auto; background-color: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);">
  <!-- Header -->
  <div style="background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%); padding: 24px; text-align: center;">
    <h1 style="margin: 0; color: white; font-size: 24px; font-weight: 600;">XÁC NHẬN ĐẶT PHÒNG THÀNH CÔNG</h1>
  </div>
  
  <!-- Content -->
  <div style="padding: 24px;">
    <p style="font-size: 16px; line-height: 1.6; color: #4b5563;">Cảm ơn bạn đã đặt phòng thông qua hệ thống của chúng tôi. Dưới đây là thông tin chi tiết về đặt phòng của bạn:</p>

    <!-- Booking Info Card -->
    <div style="background-color: #f8fafc; border-radius: 8px; padding: 20px; margin: 20px 0; border-left: 4px solid #2563eb;">
      <h3 style="margin-top: 0; margin-bottom: 16px; color: #1e293b; font-size: 18px; font-weight: 600;">THÔNG TIN ĐẶT PHÒNG</h3>
      
      <table style="width: 100%; border-collapse: collapse;">
        <tr>
          <td style="padding: 8px 0; color: #64748b; width: 40%;">Tên phòng:</td>
          <td style="padding: 8px 0; color: #1e293b; font-weight: 500;">{{ $roomName }}</td>
        </tr>
        <tr>
          <td style="padding: 8px 0; color: #64748b;">Ngày bắt đầu:</td>
          <td style="padding: 8px 0; color: #1e293b; font-weight: 500;">{{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }}</td>
        </tr>
        <tr>
          <td style="padding: 8px 0; color: #64748b;">Thời hạn thuê:</td>
          <td style="padding: 8px 0; color: #1e293b; font-weight: 500;">{{ $duration }} tháng</td>
        </tr>
        <tr>
          <td style="padding: 8px 0; color: #64748b;">Tổng chi phí:</td>
          <td style="padding: 8px 0; color: #1e293b; font-weight: 500; font-size: 18px; color: #2563eb;">{{ $totalCost }}</td>
        </tr>
      </table>
    </div>

    <!-- Additional Info -->
    <p style="font-size: 15px; line-height: 1.6; color: #4b5563;">
      Hợp đồng thuê phòng đã được tạo thành công. Vui lòng kiểm tra lại thông tin và liên hệ với chúng tôi nếu có bất kỳ sai sót nào.
    </p>
    
    <!-- Contact Info -->
    <div style="margin-top: 24px; padding: 16px; background-color: #f1f5f9; border-radius: 8px;">
      <h4 style="margin-top: 0; margin-bottom: 12px; color: #1e293b; font-size: 16px; font-weight: 600;">THÔNG TIN LIÊN HỆ</h4>
      <p style="margin: 8px 0; color: #4b5563;">Email: HOMECONVENIENT0508@gmail.com</p>
      <p style="margin: 8px 0; color: #4b5563;">Hotline: 0123456789</p>
    </div>
  </div>
  
  <!-- Footer -->
  <div style="background-color: #f1f5f9; padding: 16px; text-align: center; font-size: 14px; color: #64748b;">
    <p style="margin: 0;">Trân trọng,</p>
    <p style="margin: 4px 0 0; font-weight: 600; color: #1e293b;">Đội ngũ quản lý nhà trọ</p>
  </div>
</div>