% Financial knowledge base with more comprehensive categories

% Income categories
income_category(Income, low) :- Income =< 200000.
income_category(Income, medium) :- Income > 200000, Income =< 1000000.
income_category(Income, high) :- Income > 1000000.

% Expense categories
expense_category(Expenses, low) :- Expenses =< 200000.
expense_category(Expenses, medium) :- Expenses > 200000, Expenses =< 500000.
expense_category(Expenses, high) :- Expenses > 500000.

% Financial advice based on income and expenses
financial_advice(low, low, 'Save small, reduce unnecessary expenses.').
financial_advice(low, medium, 'Cut down expenses to avoid debt.').
financial_advice(low, high, 'Reduce expenses or find additional income sources.').

financial_advice(medium, low, 'Good financial balance, consider saving or investing.').
financial_advice(medium, medium, 'Reduce expenses, invest in savings.').
financial_advice(medium, high, 'You are spending too much, reduce expenses.').

financial_advice(high, low, 'Consider high-return investments, you have a good balance.').
financial_advice(high, medium, 'Focus on long-term savings and investments.').
financial_advice(high, high, 'Although you earn well, you are spending a lot. Reduce unnecessary expenses.').

% Advice for savings
savings_potential(Income, Expenses, Advice) :-
    Savings is Income - Expenses,
    (   Savings < 0
    ->  Advice = 'You are in deficit. Increase income or reduce expenses immediately.'
    ;   Savings >= 0, Savings < 100000
    ->  Advice = 'Your savings are low. Try to cut expenses to save more.'
    ;   Savings >= 100000, Savings < 300000
    ->  Advice = 'You have a decent amount of savings. Consider low-risk investments.'
    ;   Savings >= 300000
    ->  Advice = 'You have a good amount of savings. Consider diversifying your investments.'
    ).

% Simple Interest
simple_interest(P, R, T, Interest) :-
    Interest is (P * R * T) / 100.

% Compound Interest
compound_interest(P, R, T, CI) :-
    A is P * (1 + R / 100) ** T,
    CI is A - P.

% Loan EMI (Monthly Installment)
emi(Principal, Rate, Time, EMI) :-
    RateDecimal is Rate / 1200,  % Monthly interest rate
    N is Time * 12,              % Total number of payments
    Numerator is Principal * RateDecimal * ((1 + RateDecimal) ** N),
    Denominator is ((1 + RateDecimal) ** N) - 1,
    EMI is Numerator / Denominator.

% Savings after n years with monthly deposits
future_savings(MonthlyDeposit, Rate, Years, FutureValue) :-
    RateDecimal is Rate / 1200,       % Convert annual rate to monthly decimal rate
    N is Years * 12,                       % Total number of monthly deposits
    FutureValue is MonthlyDeposit * ((1 + RateDecimal)^N - 1) / RateDecimal.
    FutureValue is MonthlyDeposit * ((1 + RateDecimal) ^ (Time * 12) - 1) / RateDecimal * (1 + RateDecimal).

% Future value of monthly deposits
future_savings(MonthlyDeposit, Rate, Years, FutureValue) :-
    RateDecimal is Rate / 1200,           % Convert annual rate to monthly rate
    N is Years * 12,                     % Total number of months
    FutureValue is MonthlyDeposit * ((1 + RateDecimal)^N - 1) / RateDecimal.

% Future value of current savings
future_value_of_savings(CurrentSavings, Rate, Years, FutureValue) :-
    RateDecimal is Rate / 1200,           % Convert annual rate to monthly rate
    N is Years * 12,                     % Total number of months
    FutureValue is CurrentSavings * (1 + RateDecimal)^N.

% Total retirement fund calculation
retirement_fund(CurrentSavings, MonthlyDeposit, Rate, Years, RetirementFund) :-
    future_value_of_savings(CurrentSavings, Rate, Years, FV_Savings),
    future_savings(MonthlyDeposit, Rate, Years, FV_Deposits),
    RetirementFund is FV_Savings + FV_Deposits.

% Debt to income ratio (DTI)
debt_to_income(Debt, Income, DTI) :-
    DTI is (Debt / Income) * 100.

% Rule to run financial advice and other financial calculations
run_financial_advice(Income, Expenses) :-
    income_category(Income, IncomeCategory),
    expense_category(Expenses, ExpenseCategory),
    financial_advice(IncomeCategory, ExpenseCategory, BasicAdvice),
    savings_potential(Income, Expenses, SavingsAdvice),
    format('~w~n~w~n', [BasicAdvice, SavingsAdvice]).

run_simple_interest(P, R, T) :-
    simple_interest(P, R, T, Interest),
    format('The simple interest is ~2f~n', [Interest]).

run_compound_interest(P, R, T) :-
    compound_interest(P, R, T, CI),
    format('The annual compound interest is ~2f~n', [CI]).

run_emi(Principal, Rate, Time) :-
    emi(Principal, Rate, Time, EMI),
    format('The monthly EMI is ~2f~n', [EMI]).

run_future_savings(MonthlyDeposit, Rate, Time) :-
    future_savings(MonthlyDeposit, Rate, Time, FutureValue),
    format('The future value of your savings is ~2f~n', [FutureValue]).

run_retirement_fund(CurrentSavings, MonthlyDeposit, Rate, Years) :-
    retirement_fund(CurrentSavings, MonthlyDeposit, Rate, Years, RetirementFund),
    format('The estimated retirement fund is ~2f~n', [RetirementFund]).

run_dti(Debt, Income) :-
    debt_to_income(Debt, Income, DTI),
    format('Your Debt-to-Income (DTI) ratio is ~2f%~n', [DTI]).