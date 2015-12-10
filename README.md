1/ Đăng ký/đăng nhập
URL: http://107.155.88.38/gift/api/register

Tham số POST:
- fb_uid: uid của FB sau khi login trên phone
- fb_email: email của FB sau khi login trên phone

Trả về JSON:
- message: "Please provide Facebook UID and Facebook email!" => chưa post đủ uid và email
- message: "Register successfully!" => lần đầu tiên là register, trả về user_id tạo trên server
- message: "Login successfully!" => các lần sau là login, trả về user_id tạo lần trước trên server

2/ Danh sách AMZ categories
URL: http://107.155.88.38/gift/api/amz_categories

Tham số POST: ko cần

Trả về JSON:
Thành công:
- status = 1 
- categories: chuỗi json chứa danh sách category với
--> category_key là biến POST sử dụng để truyền vào hàm (3)
--> category_value là giá trị hiển thị lên select box
Thất bại:
- status = 0

3/ Danh sách AMZ products
URL: http://107.155.88.38/gift/api/search_amz_products

Tham số POST:
- search: chuỗi tìm kiếm
- category: category_key ở hàm (2)
- page: nếu là search ban đầu page mặc định là 1, nếu bấm next page trên phone page sẽ tăng lên 1

Trả về JSON
Thành công:
- status = 1
- total_products: tổng số kết quả trả về
- total_pages: tổng số trang => dùng để phân trang trên phone
- products: chuỗi json chứa danh sách product với
--> product_name: tên sản phẩm
--> product_image: hình sản phẩm
--> product_price: giá sản phẩm (USD)
--> product_link: link tới AMZ
Thất bại:
- status = 0

4/ Tạo event
URL: http://107.155.88.38/gift/api/create_event

Tham số POST: 
- user_id: lấy từ hàm register
- event_name: tên event
- event_day: ngày event, format theo kiểu yyyy-mm-dd
- event_location: địa điểm event
- product_json: chuỗi json giống như vầy http://107.155.88.38/gift/ex_json.txt (khi search xong, bấm add event thì trên phone sẽ đưa hết các product được check vào checkbox vào mảng xong rồi encode nó, tham khảo mẫu json ở hàm search_amz_products)

Trả về JSON:
Thành công:
- status = 1 
- event_id: id event đc tạo trên server
--> category_key là biến POST sử dụng để truyền vào hàm (3)
--> category_value là giá trị hiển thị lên select box
Thất bại:
- status = 0

5/ Danh sách event
URL: http://107.155.88.38/gift/api/get_events

Tham số POST: 
- user_id: lấy từ hàm register

Trả về JSON:
Thành công:
- status = 1 
- events: chuỗi json chứa danh sách event
--> event_id: id của event trên server
--> event_name: tên event
Thất bại:
- status = 0

6/ Danh sách sản phẩm của event (dùng khi xem bấm vào edit event)
URL: http://107.155.88.38/gift/api/get_products

Tham số POST: 
- user_id: lấy từ hàm register
- event_id: lấy từ hàm (5)

Trả về JSON:
Thành công:
- status = 1 
- products: chuỗi json chứa danh sách product
Thất bại:
- status = 0

7/ Xóa event
URL: http://107.155.88.38/gift/api/delete_event

Tham số POST: 
- user_id: lấy từ hàm register
- event_id: lấy từ hàm (5)

Trả về JSON:
Thành công:
- status = 1 
Thất bại:
- status = 0