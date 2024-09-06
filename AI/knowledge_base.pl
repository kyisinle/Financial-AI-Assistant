% knowledge_base.pl

% Facts
fact(stock_price('AAPL', 145.09)).
fact(stock_price('GOOGL', 2729.00)).

fact(interest_rate(0.05)). % Example interest rate

% Rules
get_stock_price(Symbol, Price) :-
    fact(stock_price(Symbol, Price)).

calculate_compound_interest(Principal, Rate, Time, Amount, Interest) :-
    Amount is Principal * (1 + Rate) ** Time,
    Interest is Amount - Principal.

calculate_simple_interest(Principal, Rate, Time, Amount, Interest) :-
    Interest is (Principal * Rate * Time) / 100,
    Amount is Principal + Interest.

% Currency conversion rules (for demonstration purposes)
convert_currency(Amount, 'USD', 'EUR', ConvertedAmount) :-
    ConversionRate is 0.85, % Example conversion rate
    ConvertedAmount is Amount * ConversionRate.
