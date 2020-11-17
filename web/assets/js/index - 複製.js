$(document).ready(function(){
    if($('#allBorrowingList').length>0){
        getAllBorrowingList();
    }
    
    if($('#queryBorrowing').length>0){
        clickQueryBorrowButton();
    }

    if($('#borrowingReport1').length>0){
        getReport1();
    }    
});

function getAllBorrowingList()
{

    $.ajax({
        method: "POST",
        url: "/getAllBorrowingData"
    })
    .done(function( data ) {
        buildBorrowingList(data);
    });   
}

function buildBorrowingList(data){
    $('#borrowingTable tr:has("td")').remove();
    $.each(data,function(idx,item){
       returnDate = (item.return_date==null)?'':item.return_date;
       console.log(returnDate);
       html = '<tr>';
       html += '<td>'+item.device_name+'</td>';
       html += '<td>'+item.borrower_name+'</td>';
       html += '<td>'+item.borrowing_date+'</td>';
       html += '<td>'+returnDate+'</td>';
       html += '</tr>';
       $('#borrowingTable tbody').append(html);
    });
}

function clickQueryBorrowButton(){
    $('#queryBorrowing button[name="submitQuery"]').on('click',function(){
        keyword = $('#queryKeywordinput').val();
        getQueryBorrowing(keyword);  
    });
}

function getQueryBorrowing(keyword){
    $.ajax({
        method: "POST",
        url: "/getQueryBorrowingData",
        data:{keyword:keyword}
    })
    .done(function( data ) {
        buildQueryBorrowingList(data)
    });     
}

function buildQueryBorrowingList(data){
    $('#queryBorrowingTable tr:has("td")').remove();
    $.each(data,function(idx,item){
       returnDate = (item.return_date==null)?'':item.return_date;
       html = '<tr>';
       html += '<td>'+item.device_name+'</td>';
       html += '<td>'+item.borrower_name+'</td>';
       html += '<td>'+item.borrowing_date+'</td>';
       html += '<td>'+returnDate+'</td>';
       html += '</tr>';
       $('#queryBorrowingTable tbody').append(html);
    });
}

function getReport1(){
    $.ajax({
        method: "POST",
        url: "/getReport1"
    })
    .done(function( data ) {
        buildReport1(data);
    });
}

function buildReport1(data){
    $('#borrowingReport1Table tr:has("td")').remove();
    $.each(data,function(idx,item){
       html = '<tr>';
       html += '<td>'+item.device_name+'</td>';
       html += '<td>'+item.total_num+'</td>';
       html += '<td>'+item.damage_num+'</td>';
       html += '<td>'+item.borrowing_num+'</td>';
       html += '</tr>';
       $('#borrowingReport1Table tbody').append(html);
    });

}