# Build Stage
FROM mcr.microsoft.com/dotnet/sdk:10.0 AS build-env
WORKDIR /src

# Copy solution and project files
COPY ["LoreTest.slnx", "./"]
COPY ["LoreTest/LoreTest.csproj", "LoreTest/"]
COPY ["LoreTest.Tests/LoreTest.Tests.csproj", "LoreTest.Tests/"]

# Restore dependencies
RUN dotnet restore "LoreTest/LoreTest.csproj"

# Copy everything else and build
COPY . .
WORKDIR "/src/LoreTest"
RUN dotnet build "LoreTest.csproj" -c Release -o /app/build

# Publish Stage
FROM build-env AS publish
RUN dotnet publish "LoreTest.csproj" -c Release -o /app/publish /p:UseAppHost=false

# Final Stage
FROM mcr.microsoft.com/dotnet/aspnet:10.0
WORKDIR /app
COPY --from=publish /app/publish .

# Expose ports
EXPOSE 8080
EXPOSE 8081

ENTRYPOINT ["dotnet", "LoreTest.dll"]
