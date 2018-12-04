using System;
using ManyConsole;

namespace Windmill
{
    public class StatisticsCommand: ConsoleCommand
    {
        public string all;

        public StatisticsCommand()
        {
            IsCommand("statistics");

            HasOption(
                "a|all",
                "Lists all statistics, including details",
                v => all = v
            );
        }

        public override int Run(string[] remainingArguments)
        {
            if (!string.IsNullOrWhiteSpace(all))
            {
                Console.WriteLine("All statistics will be shown");
            }

            return 0;
        }
    }
}
