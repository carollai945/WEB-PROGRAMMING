var allBorrowingList = new Vue({
  el: '#allBorrowingList',
  data: {
    borrowingList: null
  },
  methods: {
      getAllBorrowingList: function(){
        var self = this; 
        $.ajax({
            method: "POST",
            url: "/getAllBorrowingData"
        })
        .done(function( data ) {
            self.borrowingList = data;
        }); 
      },
      init:function(){
          var self = this;
          self.getAllBorrowingList();
      }
  }
})

allBorrowingList.init();