@extends('customer.layout')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <h2 class="mb-3">Your Plans</h2>
            
            @if($customerPlans->isEmpty())
                <div class="alert alert-info">
                    <p>You don't have any plans assigned yet.</p>
                </div>
            @else
                <div class="row">
                    @foreach($customerPlans as $plan)
                        <div class="col-md-6 mb-4">
                            <div class="card h-100">
                                <div class="card-body">
                                    <h5 class="card-title">{{ ucfirst($plan->plan_type) }} Plan</h5>
                                    
                                    <ul class="list-unstyled">
                                        <li><strong>Start Date:</strong> {{ $plan->start_date->format('M d, Y') }}</li>
                                        <li><strong>Next Due Date:</strong> {{ $plan->next_due_date->format('M d, Y') }}</li>
                                        <li><strong>Status:</strong> 
                                            <span class="badge {{ $plan->status === 'active' ? 'bg-success' : 'bg-secondary' }}">
                                                {{ ucfirst($plan->status) }}
                                            </span>
                                        </li>
                                    </ul>
                                    
                                    <div class="mt-3">
                                        <small class="text-muted">
                                            Plan created on {{ $plan->created_at->format('M d, Y') }}
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                
                <div class="d-flex justify-content-center">
                    {{ $customerPlans->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection