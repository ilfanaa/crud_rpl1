<?php
namespace App\Http\Controllers;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
// tambahkan ini
use Illuminate\Support\Facades\Storage;
class PostController extends Controller
{
    public function index():View
    {
    //get posts
    $posts = Post::latest()->paginate(5);
    
    //render view with posts
    return view('posts.index', compact('posts'));
    //
}
//langkah berikutnya
public function create() :View
{
    return view('posts.create');
}
/**
 * store
 * 
 * @param Request $request
 * @return void
 */
public function store(Request $request): RedirectResponse
{
    //validate form
    $this->validate($request, [
        'image'    => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        'title'    => 'required|min:5',
        'content'  => 'required|min:10'
    ]);
    //upload image
    $image = $request->file('image');
    $image->storeAs('public/posts', $image->hashName());
    //create post
    Post::create([
        'image'    => $image->hashName(),
        'title'    => $request->title,
        'content'  => $request->content
    ]);
    return redirect()->route('posts.index')->with(['success' => 'Data Berhasil Disimpan!']);
}
//langkah setelah membuat create diviewpost
/**
 * edit
 * 
 * @param  mixed $post
 * @return void
 */
public function edit(Post $post):View
{
    return view('posts.edit', compact('post'));
}
public function update(Request $request, Post $post): RedirectResponse
{
    //validate form
    $this->validate($request, [
        'image'    => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        'title'    => 'required|min:5',
        'content'  => 'required|min:10'
    ]);
    //check if image is uploaded
    if ($request->hasFile('image')) {
        //upload new image
        $image = $request->file('image');
        $image->storeAs('public/posts', $image->hashName());
        //delete old image
        Storage::delete('public/posts/'.$post->image);
        //update post with new image
        $post->update([
            'image'   => $image->hashName(),
            'title'   => $request->title,
            'content' => $request->content
        ]);

    } else {
        
        //update post without image
        $post->update([
            'title'   => $request->title,
            'content' => $request->content
        ]);
    }
    //redirect to index
    return redirect()->route('posts.index')->with(['success' => 'Data Berhasil Diubah!']);

    //lalu buat edit di viewposts
}
    public function destroy(Post $post):RedirectResponse
    {
        //delete image
        Storage::delete('public/posts/'. $post->image);
        //delete post
        $post->delete();
        //redirect to index
        return redirect()->route('posts.index')->with(['success' => 'Data Berhasil Dihapus!']);
    }
    public function show(string $id):View
    {
        //get post by ID
        $post = Post::findOrFail($id);
        //render view with post
        return view('posts.show', compact('post'));
    }

}
