<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Models\Contact;
use App\Mail\ContacMail;
use Illuminate\Support\Facades\Validator;

use App\Models\Category;
use App\Models\Post;

class ContactController extends Controller
{
    public function create(){
        $category_new = Category::where('name','!=','Chưa phân loại')->orderBy('id','DESC')->take(4)->get();
        foreach($category_new as $category_new_item ){
            $posts_new_category[] = Post::where('category_id',$category_new_item->id)->orderBy('created_at','DESC')->take(1)->get();
        }
        
        return view('contact',[
            'categories' => $categories = Category::where('name','!=','Chưa phân loại')->orderBy('created_at','DESC')->take(10)->get(),
            'posts_new_category' => $posts_new_category,
        ]);
    }

    public function store(){

        $data = array();
        $data['success'] = 0;
        $data['errors'] = [];
 
 
        $rules = [
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required',
            // 'subject' => 'nullable|min:5|max:50',
            'subject' => 'required|min:5|max:50',
            'message' => 'required|min:5|max:500' ,
        ];

        $validated = Validator::make( request()->all(), $rules);
  

        if($validated->fails()){
            $data['errors']['first_name'] = $validated->errors()->first('first_name');
            $data['errors']['last_name'] = $validated->errors()->first('last_name');
            $data['errors']['email'] = $validated->errors()->first('email');
            $data['errors']['subject'] = $validated->errors()->first('subject');
            $data['errors']['message'] = $validated->errors()->first('message');

            $data['message'] = "Thông báo lỗi: kiểm tra thông tin và nhập lại lần nữa";

        }else{
            $attributes = $validated->validated();
            Contact::create($attributes);

            // Mail::to("anhtuanlop10a2812001@gmail.com")->send(new ContacMail(
            Mail::to( env('ADMIN_EMAIL') )->send(new ContacMail(
                $attributes['first_name'],
                $attributes['last_name'],
                $attributes['email'],
                $attributes['subject'],
                $attributes['message']
            ));
    
            $data['success'] = 1;
            $data['message'] = "Bạn đã gửi liên hệ thành công. Chúng tôi sẽ phản hổi cho bạn sớm nhất có thể";

        }
        

        // return redirect()->route('contact.create')->with('success', 
        // 'Bạn đã gửi liên hệ thành công. Chúng tôi sẽ phản hổi cho bạn sớm nhất có thể !');

        return response()->json($data);
    }
}
