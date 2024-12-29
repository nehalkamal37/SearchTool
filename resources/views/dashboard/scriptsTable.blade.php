@extends('dashboard.app')

@section('title', 'Dashboard')

@section('content')


    <!-- End Navbar -->
    <div class="container-fluid py-2">
     
      <div class="row">
        <div class="col-12">
          <div class="card my-4">
            <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
              <div class="bg-gradient-dark shadow-dark border-radius-lg pt-4 pb-3">
                <h6 class="text-white text-capitalize ps-3">Scripts table
                  
                </h6>

              </div>
              <a href="{{route('dashboard.scripts.create')}}" class="text-secondary font-weight-bold text-xs" >
                <h6>ADD SCRIPT</h6>
                </a>
              <form method="GET" action="{{ route('dashboard.scripts.index') }}" class="mb-3">
                @csrf
                <div class="input-group">
                    <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Search by drug name">
                    <button type="submit" class="btn btn-primary">Search</button>
                </div>
            </form>
            
            </div>
            <div class="card-body px-0 pb-2">
              <div class="table-responsive p-0">
                <table class="table align-items-center justify-content-center mb-0">
                  <thead>
                    <tr>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">drug name</th>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">script</th>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">class</th>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Insurance</th>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder text-center opacity-7 ps-2">date</th>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder text-center opacity-7 ps-2">actions</th>

                      <th></th>
                    </tr>
                  </thead>
                  <tbody>
                    @forelse($scripts as $script)
                    <tr>
                      <td>
                        <div class="d-flex px-2">
                          <div>
                          </div>
                          <div class="my-auto">
                            <h6 class="mb-0 text-sm">{{$script->Drug_Name}}</h6>
                          </div>
                        </div>
                      </td>
                      <td>
                        <p class="text-sm font-weight-bold mb-0">{{$script->Script}}</p>
                      </td>
                      <td>
                        <span class="text-xs font-weight-bold">{{$script->Class}}</span>
                      </td>
                      <td>
                        <span class="text-xs font-weight-bold">{{$script->insurance_name   }}        </span>
                      </td>
                      <td class="align-middle text-center">
                        <div class="d-flex align-items-center justify-content-center">
                          <span class="me-2 text-xs font-weight-bold">{{ $script->formatted_date }}</span>
                          <div>
                            
                            <td class="align-middle">
                              <a href="{{route('dashboard.scripts.edit',$script->id)}}" class="text-secondary font-weight-bold text-xs" data-toggle="tooltip" data-original-title="Edit user">
                                <p style="color: blue"> Edit</p>
                                
                              </a>
                              <form action="{{ route('dashboard.scripts.destroy', $script->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this drug?');">
                                @csrf
                                @method('DELETE') <!-- Spoof DELETE method -->
                                <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                            </form>
                            
                            </td>
                          </div>
                        </div>
                      </td>
                      <td class="align-middle">
                        <button class="btn btn-link text-secondary mb-0">
                          <i class="fa fa-ellipsis-v text-xs"></i>
                        </button>
                      </td>
                    </tr>
                    @empty
        <tr>
            <td colspan="4" class="text-center">No scripts found</td>
        </tr>
        @endforelse
      
                  </tbody>
                </table>
                <div class="mt-4">
                  {{ $scripts->links() }}
              </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      @endsection
   