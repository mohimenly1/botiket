<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ProductTest extends TestCase
{
 // use RefreshDatabase;

    /**
     * A basic test example.
     *
     * @return void
     */

    //valid 
    public function test_Create_Product()
    {
       $response = $this->post('/brands',[
           "name"=>"asfa ghd",
           "selling_currency_id"=>69,
           "original_currency_id"=>5,
           "increase_percentage"=>1.2
       ],[
          "Authorization"=> "Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC82NzY2LTEwMi0yMjEtOC0xOC5uZ3Jvay5pb1wvYWRtaW5cL2xvZ2luIiwiaWF0IjoxNjQ0MTQ4NzM3LCJuYmYiOjE2NDQxNDg3MzcsImp0aSI6InRSRldJbVA3NnhsUkQ3WWkiLCJzdWIiOjEsInBydiI6IjIzYmQ1Yzg5NDlmNjAwYWRiMzllNzAxYzQwMDg3MmRiN2E1OTc2ZjcifQ.lCUOKsxrDp6pB5h9J4DnNh4trlRbTDRWdtX2rmN3Ym8"
       ]);

        //$response->assertStatus(200);
        $response->assertStatus(200); 

        return $response["data"]["id"];
    }


    //invalid woring info
    public function test_Create_Product_invalid()
    {
       $response = $this->post('/brands',[
           "name"=>"asfa ghd",
           "selling_currency_id"=> -5,
           "original_currency_id"=>5,
           "increase_percentage"=>1.2
       ],[
          "Authorization"=> "Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC82NzY2LTEwMi0yMjEtOC0xOC5uZ3Jvay5pb1wvYWRtaW5cL2xvZ2luIiwiaWF0IjoxNjQ0MTQ4NzM3LCJuYmYiOjE2NDQxNDg3MzcsImp0aSI6InRSRldJbVA3NnhsUkQ3WWkiLCJzdWIiOjEsInBydiI6IjIzYmQ1Yzg5NDlmNjAwYWRiMzllNzAxYzQwMDg3MmRiN2E1OTc2ZjcifQ.lCUOKsxrDp6pB5h9J4DnNh4trlRbTDRWdtX2rmN3Ym8"
       ]);

        //$response->assertStatus(200);
        $response->assertStatus(302); 

    }
    public function test_Create_Brand_invalid_selling_currency_id_2()
    {
       $response = $this->post('/brands',[
           "name"=>"asfa ghd",
           "selling_currency_id"=> 2,
           "original_currency_id"=>5,
           "increase_percentage"=>1.2
       ],[
          "Authorization"=> "Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC82NzY2LTEwMi0yMjEtOC0xOC5uZ3Jvay5pb1wvYWRtaW5cL2xvZ2luIiwiaWF0IjoxNjQ0MTQ4NzM3LCJuYmYiOjE2NDQxNDg3MzcsImp0aSI6InRSRldJbVA3NnhsUkQ3WWkiLCJzdWIiOjEsInBydiI6IjIzYmQ1Yzg5NDlmNjAwYWRiMzllNzAxYzQwMDg3MmRiN2E1OTc2ZjcifQ.lCUOKsxrDp6pB5h9J4DnNh4trlRbTDRWdtX2rmN3Ym8"
       ]);

        //$response->assertStatus(200);
        $response->assertStatus(400); 

    }
    public function test_Create_Brand_invalid_original_currency_id()
    {
       $response = $this->post('/brands',[
           "name"=>"asfa ghd",
           "selling_currency_id"=>69,
           "original_currency_id"=>-5,
           "increase_percentage"=>1.2
       ],[
          "Authorization"=> "Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC82NzY2LTEwMi0yMjEtOC0xOC5uZ3Jvay5pb1wvYWRtaW5cL2xvZ2luIiwiaWF0IjoxNjQ0MTQ4NzM3LCJuYmYiOjE2NDQxNDg3MzcsImp0aSI6InRSRldJbVA3NnhsUkQ3WWkiLCJzdWIiOjEsInBydiI6IjIzYmQ1Yzg5NDlmNjAwYWRiMzllNzAxYzQwMDg3MmRiN2E1OTc2ZjcifQ.lCUOKsxrDp6pB5h9J4DnNh4trlRbTDRWdtX2rmN3Ym8"
       ]);

        //$response->assertStatus(200);
        $response->assertStatus(302); 

    }
    public function test_Create_Brand_invalid_increase_percentage()
    {
       $response = $this->post('/brands',[
           "name"=>"asfa ghd",
           "selling_currency_id"=>69,
           "original_currency_id"=>-5,
           "increase_percentage"=>-5,
       ],[
          "Authorization"=> "Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC82NzY2LTEwMi0yMjEtOC0xOC5uZ3Jvay5pb1wvYWRtaW5cL2xvZ2luIiwiaWF0IjoxNjQ0MTQ4NzM3LCJuYmYiOjE2NDQxNDg3MzcsImp0aSI6InRSRldJbVA3NnhsUkQ3WWkiLCJzdWIiOjEsInBydiI6IjIzYmQ1Yzg5NDlmNjAwYWRiMzllNzAxYzQwMDg3MmRiN2E1OTc2ZjcifQ.lCUOKsxrDp6pB5h9J4DnNh4trlRbTDRWdtX2rmN3Ym8"
       ]);

        //$response->assertStatus(200);
        $response->assertStatus(302); 

    }

    //invalid missing info

    public function test_Create_Brand_invalid_no_selling_currency_id()
    {
       $response = $this->post('/brands',[
           "name"=>"asfa ghd",
           "original_currency_id"=>5,
           "increase_percentage"=>1.2
       ],[
          "Authorization"=> "Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC82NzY2LTEwMi0yMjEtOC0xOC5uZ3Jvay5pb1wvYWRtaW5cL2xvZ2luIiwiaWF0IjoxNjQ0MTQ4NzM3LCJuYmYiOjE2NDQxNDg3MzcsImp0aSI6InRSRldJbVA3NnhsUkQ3WWkiLCJzdWIiOjEsInBydiI6IjIzYmQ1Yzg5NDlmNjAwYWRiMzllNzAxYzQwMDg3MmRiN2E1OTc2ZjcifQ.lCUOKsxrDp6pB5h9J4DnNh4trlRbTDRWdtX2rmN3Ym8"
       ]);

        //$response->assertStatus(200);
        $response->assertStatus(302); 

    }
    public function test_Create_Brand_invalid_no_original_currency_id()
    {
       $response = $this->post('/brands',[
           "name"=>"asfa ghd",
           "selling_currency_id"=>69,
           "increase_percentage"=>1.2
       ],[
          "Authorization"=> "Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC82NzY2LTEwMi0yMjEtOC0xOC5uZ3Jvay5pb1wvYWRtaW5cL2xvZ2luIiwiaWF0IjoxNjQ0MTQ4NzM3LCJuYmYiOjE2NDQxNDg3MzcsImp0aSI6InRSRldJbVA3NnhsUkQ3WWkiLCJzdWIiOjEsInBydiI6IjIzYmQ1Yzg5NDlmNjAwYWRiMzllNzAxYzQwMDg3MmRiN2E1OTc2ZjcifQ.lCUOKsxrDp6pB5h9J4DnNh4trlRbTDRWdtX2rmN3Ym8"
       ]);

        //$response->assertStatus(200);
        $response->assertStatus(302); 

    }
    public function test_Create_Brand_invalid_no_increase_percentage()
    {
       $response = $this->post('/brands',[
           "name"=>"asfa ghd",
           "selling_currency_id"=>69,
           "original_currency_id"=>-5,
       ],[
          "Authorization"=> "Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC82NzY2LTEwMi0yMjEtOC0xOC5uZ3Jvay5pb1wvYWRtaW5cL2xvZ2luIiwiaWF0IjoxNjQ0MTQ4NzM3LCJuYmYiOjE2NDQxNDg3MzcsImp0aSI6InRSRldJbVA3NnhsUkQ3WWkiLCJzdWIiOjEsInBydiI6IjIzYmQ1Yzg5NDlmNjAwYWRiMzllNzAxYzQwMDg3MmRiN2E1OTc2ZjcifQ.lCUOKsxrDp6pB5h9J4DnNh4trlRbTDRWdtX2rmN3Ym8"
       ]);

        //$response->assertStatus(200);
        $response->assertStatus(302); 

    }
    





     /**
     * @depends test_Create_Brand
     */
    
    //valid 
    public function test_Update_Percentage_Valid($id)
    {
       $response = $this->post('/brands/update_percentage',[
           "id"=>$id,
           "increase_percentage"=>20
       ],[
        "Authorization"=> "Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC82NzY2LTEwMi0yMjEtOC0xOC5uZ3Jvay5pb1wvYWRtaW5cL2xvZ2luIiwiaWF0IjoxNjQ0MTQ4NzM3LCJuYmYiOjE2NDQxNDg3MzcsImp0aSI6InRSRldJbVA3NnhsUkQ3WWkiLCJzdWIiOjEsInBydiI6IjIzYmQ1Yzg5NDlmNjAwYWRiMzllNzAxYzQwMDg3MmRiN2E1OTc2ZjcifQ.lCUOKsxrDp6pB5h9J4DnNh4trlRbTDRWdtX2rmN3Ym8"
    ]);

        //$response->assertStatus(200);
        $response->assertStatus(200); 
    }



    //invalid missing info
    public function test_Update_Percentage_InValid_noID()
    {
       $response = $this->post('/brands/update_percentage',[
           "increase_percentage"=>20
       ],[
        "Authorization"=> "Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC82NzY2LTEwMi0yMjEtOC0xOC5uZ3Jvay5pb1wvYWRtaW5cL2xvZ2luIiwiaWF0IjoxNjQ0MTQ4NzM3LCJuYmYiOjE2NDQxNDg3MzcsImp0aSI6InRSRldJbVA3NnhsUkQ3WWkiLCJzdWIiOjEsInBydiI6IjIzYmQ1Yzg5NDlmNjAwYWRiMzllNzAxYzQwMDg3MmRiN2E1OTc2ZjcifQ.lCUOKsxrDp6pB5h9J4DnNh4trlRbTDRWdtX2rmN3Ym8"
    ]);

        //$response->assertStatus(200);
        $response->assertStatus(400); 
    }
    public function test_Update_Percentage_InValid_No_Percentage()
    {
       $response = $this->post('/brands/update_percentage',[
           "id"=>1,
       ],[
        "Authorization"=> "Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC82NzY2LTEwMi0yMjEtOC0xOC5uZ3Jvay5pb1wvYWRtaW5cL2xvZ2luIiwiaWF0IjoxNjQ0MTQ4NzM3LCJuYmYiOjE2NDQxNDg3MzcsImp0aSI6InRSRldJbVA3NnhsUkQ3WWkiLCJzdWIiOjEsInBydiI6IjIzYmQ1Yzg5NDlmNjAwYWRiMzllNzAxYzQwMDg3MmRiN2E1OTc2ZjcifQ.lCUOKsxrDp6pB5h9J4DnNh4trlRbTDRWdtX2rmN3Ym8"
    ]);

        //$response->assertStatus(200);
        $response->assertStatus(400); 
    }
    public function test_Update_Percentage_InValid_Empty_Request()
    {
       $response = $this->post('/brands/update_percentage',[
       ],[
        "Authorization"=> "Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC82NzY2LTEwMi0yMjEtOC0xOC5uZ3Jvay5pb1wvYWRtaW5cL2xvZ2luIiwiaWF0IjoxNjQ0MTQ4NzM3LCJuYmYiOjE2NDQxNDg3MzcsImp0aSI6InRSRldJbVA3NnhsUkQ3WWkiLCJzdWIiOjEsInBydiI6IjIzYmQ1Yzg5NDlmNjAwYWRiMzllNzAxYzQwMDg3MmRiN2E1OTc2ZjcifQ.lCUOKsxrDp6pB5h9J4DnNh4trlRbTDRWdtX2rmN3Ym8"
    ]);

        //$response->assertStatus(200);
        $response->assertStatus(400); 
    }


    //invalid woring info
    public function test_Update_Percentage_InValid_ID()
    {
       $response = $this->post('/brands/update_percentage',[
           "id"=>-1,
           "increase_percentage"=>20
       ],[
        "Authorization"=> "Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC82NzY2LTEwMi0yMjEtOC0xOC5uZ3Jvay5pb1wvYWRtaW5cL2xvZ2luIiwiaWF0IjoxNjQ0MTQ4NzM3LCJuYmYiOjE2NDQxNDg3MzcsImp0aSI6InRSRldJbVA3NnhsUkQ3WWkiLCJzdWIiOjEsInBydiI6IjIzYmQ1Yzg5NDlmNjAwYWRiMzllNzAxYzQwMDg3MmRiN2E1OTc2ZjcifQ.lCUOKsxrDp6pB5h9J4DnNh4trlRbTDRWdtX2rmN3Ym8"
    ]);

        //$response->assertStatus(200);
        $response->assertStatus(400); 
    }
    public function test_Update_Percentage_InValid_Percentage()
    {
       $response = $this->post('/brands/update_percentage',[
           "id"=>1,
           "increase_percentage"=>-2
       ],[
        "Authorization"=> "Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC82NzY2LTEwMi0yMjEtOC0xOC5uZ3Jvay5pb1wvYWRtaW5cL2xvZ2luIiwiaWF0IjoxNjQ0MTQ4NzM3LCJuYmYiOjE2NDQxNDg3MzcsImp0aSI6InRSRldJbVA3NnhsUkQ3WWkiLCJzdWIiOjEsInBydiI6IjIzYmQ1Yzg5NDlmNjAwYWRiMzllNzAxYzQwMDg3MmRiN2E1OTc2ZjcifQ.lCUOKsxrDp6pB5h9J4DnNh4trlRbTDRWdtX2rmN3Ym8"
    ]);

        //$response->assertStatus(200);
        $response->assertStatus(400); 
    }
}
