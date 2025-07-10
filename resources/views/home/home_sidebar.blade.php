                 <div class="col-sm-3">
                     <div class="left-sidebar">
                         <h2>Danh mục sản phẩm</h2>
                         <div class="panel-group category-products" id="category-accordian">
                             <!-- Categories will be inserted here by JavaScript -->
                             <div style="text-align: center; margin-top: 8px;">
                                 <span id="category-toggle-btn" class="toggle-link" style="display: none; cursor: pointer; color: #0fa6fe; font-weight: 500;">
                                     <i class="fa fa-chevron-down"></i> Xem thêm
                                 </span>
                             </div>
                         </div>
                         <div style="text-align: center; margin-top: 8px;">
                             <span id="category-toggle-btn" class="toggle-link" style="display: none; cursor: pointer; color: #0fa6fe; font-weight: 500;">
                                 <i class="fa fa-chevron-down"></i> Xem thêm
                             </span>
                         </div>

                         <div class="brands_products">
                             <h2>Thương hiệu sản phẩm</h2>
                             <div class="brands-name">
                                 <ul class="nav nav-pills nav-stacked" id="brands-list">
                                     <!-- Brands will be inserted here by JavaScript -->
                                 </ul>
                                 <div style="text-align: center; margin-top: 8px;">
                                     <span id="brand-toggle-btn" class="toggle-link" style="display: none; cursor: pointer; color: #0fa6fe; font-weight: 500;">
                                         <i class="fa fa-chevron-down"></i> Xem thêm
                                     </span>
                                 </div>
                             </div>

                         </div>
                     </div>
                 </div>
                 <style>
                     .left-sidebar {
                         background-color: #fff;
                         padding: 16px;
                         border-radius: 10px;
                         box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
                         margin-bottom: 20px;
                     }

                     .left-sidebar h2 {
                         font-size: 18px;
                         font-weight: 600;
                         padding-bottom: 12px;
                         border-bottom: 2px solid #f0f0f0;
                         margin-bottom: 16px;
                         color: #333;
                     }

                     .category-products .panel-group,
                     .brands-name {
                         overflow: hidden;
                         transition: max-height 0.3s ease;
                     }

                     .category-products .panel,
                     .brands-name ul li {
                         padding: 6px 0;
                         border-bottom: 1px solid #f5f5f5;
                         font-size: 14px;
                     }

                     .category-products .panel a,
                     .brands-name ul li a {
                         color: #333;
                         text-decoration: none;
                         transition: color 0.2s ease;
                     }

                     .category-products .panel a:hover,
                     .brands-name ul li a:hover {
                         color: #0fa6fe;
                     }

                     .toggle-link {
                         display: inline-block;
                         margin-top: 10px;
                         color: #0fa6fe;
                         font-size: 14px;
                         cursor: pointer;
                         transition: color 0.2s ease;
                     }

                     .toggle-link:hover {
                         color: #007acc;
                     }

                     .toggle-link i {
                         margin-right: 4px;
                     }
                 </style>