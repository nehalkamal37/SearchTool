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
                <h6 class="text-white text-capitalize ps-3">Drugs table</h6>
             
              </div>
              <div>
                <a href="{{route('dashboard.drugs.create')}}" class="text-secondary font-weight-bold text-xs" >
               <h5> ADD Drug</h5> 
                </a>
              </div>
              <form method="GET" action="{{ route('dashboard.drugs.index') }}" class="mb-3">
                @csrf
                <div class="input-group">
                    <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Search by drug name">
                    <button type="submit" class="btn btn-primary">Search</button>
                </div>
            </form>
            </div>
            <div class="card-body px-0 pb-2">
              <div class="table-responsive p-0">
                <table class="table align-items-center mb-0">
                  <thead>
                    <tr>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Drug Name</th>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">class</th>
                      <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">form</th>
                      <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">strength</th>

                      <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7"></th>
                      <th class="text-secondary opacity-7">actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    @forEach($drugs as $drug)

                    <tr>
                      <td>
                        <div class="d-flex px-2 py-1">
                          <div>
                          </div>
                          <div class="d-flex flex-column justify-content-center">
                            <h6 class="mb-0 text-sm">{{$drug->drug_name}}</h6>
                            <p class="text-xs text-secondary mb-0">ndc: {{$drug->ndc}}</p>
                          </div>
                        </div>
                      </td>
                      <td>
                        <p class="text-xs font-weight-bold mb-0">{{$drug->drug_class}}</p>
                        <p class="text-xs text-secondary mb-0">mfg: {{$drug->mfg}}</p>
                      </td>
                      <td class="align-middle text-center text-sm">
                        <p class="text-xs font-weight-bold mb-0">{{$drug->form}}</p>
                        <p class="text-xs text-secondary mb-0">acq: {{$drug->acq}}</p>

                       {{-- <span class="badge badge-sm bg-gradient-success"> 
                          
                          {{   $drugCounts[$drug->name ] ?? 0 }}
                         
                        </span>
                      --}}
                      </td>
                      <td>
                        <p class="text-xs font-weight-bold mb-0">{{$drug->strength}}</p>
                        <p class="text-xs text-secondary mb-0">RXCUI : {{$drug->rxCUI}}</p>
                      </td>
                      <td class="align-middle text-center">
                        <span class="text-secondary text-xs font-weight-bold"></span>
                       
                      </td>
                      <td class="align-middle">
                        <a href="{{route('dashboard.drugs.edit',$drug->id)}}" class="text-secondary font-weight-bold text-xs" data-toggle="tooltip" data-original-title="Edit user">
                          <p style="color: blue"> Edit</p>
                          
                        </a>
                        <form action="{{ route('dashboard.drugs.destroy', $drug->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this drug?');">
                          @csrf
                          @method('DELETE') <!-- Spoof DELETE method -->
                          <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                      </form>
                      
                      </td>
                    </tr>
                    @endforeach
                   
                  </tbody>
                </table>
                <div class="mt-4">
                  {{ $drugs->links() }}
              </div>
              </div>
            </div>
          </div>
        </div>
      </div>
     
      @endsection
   