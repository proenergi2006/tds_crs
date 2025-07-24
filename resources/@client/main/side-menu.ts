import { type Menu } from "@/stores/menu";
import { useAuthStore } from '@/stores/auth'

const menu: Array<Menu | "divider"> = [
  
    {
      icon: "User",
      pageName: "profile-overview-1",
      title: "Profile",
    },
  
  {
    icon: "Home",
    pageName: "dashboard",
    title: "Dashboard",
    subMenu: [
      {
        icon: "Activity",
        pageName: "dashboard-overview-1",
        title: "Overview 1",
      },
      {
        icon: "Activity",
        pageName: "dashboard-overview-2",
        title: "Overview 2",
      },
      {
        icon: "Activity",
        pageName: "dashboard-overview-3",
        title: "Overview 3",
      },
      {
        icon: "Activity",
        pageName: "dashboard-overview-4",
        title: "Overview 4",
      },
    ],
  },

  {
    icon: "User",
    pageName: "Access Control",
    title: "Access Control",
    subMenu: [
      {
        icon: "Shield",
        pageName: "role-overview",
        title: "role",
      },
      {
        icon: "Users",
        pageName: "users",
        title: "user",
      },
    ],
  },

  {
    icon: "CheckCircle",
    pageName: "Verifikasi",
    title: "Verifikasi",
    subMenu: [
      {
        icon: "CheckSquare",
        pageName: "po-verification-list",
        title: "Verifikasi PO",
      },
      {
        icon: "CheckSquare",
        pageName: "po-verification-detail",
        title: "Detail Verifikasi PO",
      },
     
    ],
  },

  

  {
    icon: "Users",
    pageName: "Customer",
    title: "Customer",
    subMenu: [
      {
        icon: "User",
        pageName: "customers-list",
        title: "customer",
      },
      {
        icon: "File",
        pageName: "penawarans-list",
        title: "penawaran",
      },
    ],
  },

  {
    icon: "Receipt",
    pageName: "Transactions",
    title: "Transactions-Data",
    subMenu: [
      {
        icon: "Inbox",
        pageName: "vendor-pos-list",
        title: "PO Supplier",
      },
    ],
  },
  

  {
    icon: "Boxes",
    pageName: "Inventory",
    title: "Inventory-Data",
    subMenu: [
      {
        icon: "Inbox",
        pageName: "StockInventory",
        title: "Stock Inventory",
      },
     

    ],
  },


  {
    icon: "Database",
    pageName: "Master-Data",
    title: "Master-Data",
    subMenu: [
      {
        icon: "CreditCard",
        pageName: "vendors-list",
        title: "Vendor",
      },
      {
        icon: "Terminal",
        pageName: "terminals-list",
        title: "Terminal",
      },

    ],
  },


  {
    icon: "Database",
    pageName: "Refrensi-Data",
    title: "Refrensi-Data",
    subMenu: [
      {
        icon: "Building",
        pageName: "cabang",
        title: "Cabangs",
      },
      
      // {
      //   icon: "CreditCard",
      //   pageName: "vendors-list",
      //   title: "Vendor",
      // },
      // {
      //   icon: "Terminal",
      //   pageName: "terminals-list",
      //   title: "Terminal",
      // },

      
      {
        icon: "Archive",
        pageName: "products",
        title: "Products",
        subMenu: [
          {
            icon: "Package",
            pageName: "produks-list",
            title: "Produks List",
          },
          {
            icon: "FileText",
            pageName: "satuan",
            title: "Satuans",
          },
          {
            icon: "FileText",
            pageName: "ukuran",
            title: "Ukurans",
          },
          {
            icon: "FileText",
            pageName: "jenis-produk-list",
            title: "Jenis",
          },
          
        ],
      },
      {
        icon: "Wallet",
        pageName: "Harga",
        title: "Harga",
        subMenu: [
          {
            icon: "ShoppingBag",
            pageName: "produk-hargas",
            title: "Produks Harga",
          },
          {
            icon: "Tag",
            pageName: "attachment-harga-dasar-list",
            title: "Attachment Harga",
          },
         
        ],
      },
     
     
    ],
  },

  {
    icon: "MapPin",
    pageName: "Master Wilayah",
    title: "Master Wilayah",
    subMenu: [
      {
        icon: "Flag",
        pageName: "provinsi-list",
        title: "provinsi",
      },
      {
        icon: "Flag",
        pageName: "kabupatens-list",
        title: "kabupaten",
      },
    ],
  },


  {
    icon: "Database",
    pageName: "Master Data Logistik",
    title: "Master Logistik",
    subMenu: [
      {
        icon: "Users",
        pageName: "transportir-list",
        title: "transportir",
      },
      {
        icon: "User",
        pageName: "personnel-list",
        title: "personnel",
      },
      {
        icon: "Package",
        pageName: "volumes-list",
        title: "Volume",
      },
      {
        icon: "Pin",
        pageName: "wilayah-angkut-list",
        title: "Wilayah Angkut",
      },
      {
        icon: 'Ship',
        pageName: 'kapals-list',
        title: 'Master Kapal'
      },
      {
        icon: 'Ship',
        pageName: 'ongkos-kapal-list',
        title: 'OA Kapal'
      },
      
    ],
  },
  
  

  
  


  
];

export default menu;
