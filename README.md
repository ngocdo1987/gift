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