
<div id="orderList" class="d-flex justify-content-start  p-2">
    <div class="card mr-2" style="max-width: 220px; margin-right:10px" v-for="order in items" v-cloak>
        <div class="card-header p-0 z-index-1">
          <a v-on:click="finishOrUnfinishOrder(order.id,order.isFromDbOrder,order.kds_finished)" class="d-block" >
            <div class="border-radius-lg" :style="order.header.color" style="height: 100px; width:100%;">
                <p style="margin-left: 10px; color:black" class="text-uppercase text font-weight-bold">
                    @{{order.header.title1}}
                </p>
                <p style="margin-left: 10px; color:black" class="text-sm font-weight-bold">
                    @{{order.header.title2}}
                </p>
                <p style="margin-left: 10px; color:black" class="text-sm font-weight-bold">
                    @{{order.header.title3}}
                </p>
                
            </div>
            
          </a>
        </div>
      
        <div class="card-body pt-2">
            
          <p class="card-description mb-4"  v-for="cartdata in order.cart_data" v-cloak>
            <span v-on:click="finishItem(order.id,cartdata.id,order.isFromDbOrder)" v-if="cartdata.kds_finished==0">@{{cartdata.quantity}} x  @{{cartdata.name}}</span>
            <span v-on:click="unfinishItem(order.id,cartdata.id,order.isFromDbOrder)"  style="text-decoration: line-through;" v-if="cartdata.kds_finished==1">@{{cartdata.quantity}} x  @{{cartdata.name}}</span>
          </p>
          <div  v-if="order.comment.length>2">
            <hr />
            <p class="card-description mb-4">
                @{{order.comment}}
            </p>

          </div>
          

        </div>
    </div>

     


</div>